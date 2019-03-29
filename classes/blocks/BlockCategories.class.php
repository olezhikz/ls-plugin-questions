<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Обработка блока с редактированием категорий объекта
 *
 * @package application.blocks
 * @since   2.0
 */
class PluginQuestions_BlockCategories extends Block
{
    /**
     * Запуск обработки
     */
    public function Exec()
    {
        $aCategories = $this->Category_GetCategoriesTreeByTargetType('questions');
        $this->Viewer_Assign('aCategories',$aCategories );
        $this->SetTemplate('component@questions:questions.block-categories');
    }
}