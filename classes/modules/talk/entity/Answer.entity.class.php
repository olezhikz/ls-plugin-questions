<?php


/**
 * Description of EntityVote
 *
 * @author oleg
 */
class PluginQuestions_ModuleTalk_EntityAnswer extends EntityORM{
    
    const STATE_BEST = 3;


    protected $aValidateRules = [];
   
    protected $aBehaviors = array(
        
        'like' => array(
            'class'       => 'PluginLike_ModuleLike_BehaviorEntity',
            'target_type' => 'answer',
            'title_field' => 'title',
        ),
        'moderation' => [
            'class' => 'PluginModeration_ModuleModeration_BehaviorEntity',
            'moderation_fields' => [
                'text'
            ],
            'title_field' => 'text',
            'label' => 'Ответ'
        ]
    );

    public function __construct($aParam = false)
    {
        parent::__construct($aParam);
        
        $this->aValidateRules[] =   array(
            'user_id', 
            'exist_user',
            'on' => [ 'edit', 'create']
        );
        $this->aValidateRules[] = array(
            'question_id', 
            'exist_question',
            'on' => [ 'edit', 'create']            
        );
        $this->aValidateRules[] =    array(
            'text', 
            'string', 
            'max' => 2000, 
            'min' => 10, 
            'allowEmpty' => false,
            'msg' => $this->Lang_Get('talk.response.form.text.error_validate', ['min' => 10, 'max' => 2000]),
            'on' => [ 'edit', 'create']
        );
        $this->aValidateRules[] =    array(
            'text', 
            'double_text',
            'on' => [ 'edit', 'create']
        );
        
        
    }
    
    
    protected $aRelations = array(
        'author' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id'),
        'question' => array(self::RELATION_TYPE_BELONGS_TO, 'PluginQuestions_ModuleTalk_EntityQuestion', 'question_id')
    );
    
    public function ValidateDoubleText($sValue) {
        $sParseText = $this->Text_Parser($sValue);
        
        if( $this->PluginQuestions_Talk_GetAnswerByFilter([
            '#where' => ['t.id != ?' => [($this->getId() !== null)?$this->getId():0] ],
            'text'  => $sParseText,
            'user_id' => $this->getUserId(),
            'question_id' => $this->getQuestionId()
        ])){
            return $this->Lang_Get('plugin.questions.answer.notice.error_double_text');
        }
        
        return true;
    }
    
    public function ValidateExistUser($sValue) {
        if((int)$sValue === 0 ){
            return true;
        }
        if(!$this->User_GetUserById($sValue)){
            return $this->Lang_Get('common.error.error').' user not found';
        }
        return true;
    }
    
    public function ValidateExistQuestion($sValue) {
        if(!$oQuestion = $this->PluginQuestions_Talk_GetQuestionById($sValue) ){
            return $this->Lang_Get('common.error.error').' Question not found';
        }
        
        if($this->_isNew() and $oQuestion->isClosed()){
            return $this->Lang_Get('plugin.questions.answer.notice.error_question_closed');
        }
       
        return true;
    }
    
    public function getDateCreateFormat() {
        $date = new DateTime($this->getDateCreate());
        return $date->format('d.m.y');
    }
    
    
    public function isPublish() {
        return in_array($this->getState(), [
            'publish',
        ]);
    }
    
   
    public function setBest() {
        $this->setState(self::STATE_BEST);
    }
    
    public function getUrl() {
        $oQuestion = $this->PluginQuestions_Talk_GetQuestionByFilter([
            '#with_moderation' => 1, 
            'id' => $this->getQuestionId()
        ]);
        return $oQuestion->getUrl().'#ans'.$this->getId();
    }
    
    public function getTitle() {
        return func_text_words($this->getText(), 3).'...';
    }
    
    public function isBest() {
        return ($this->getState() == self::STATE_BEST);
    }
    
    public function afterModerate() {
        $this->Hook_Run('add_answer', array('target_id' => parent::getQuestion()->getId(),'oAnswer' => $this)); 
    }
    
}
