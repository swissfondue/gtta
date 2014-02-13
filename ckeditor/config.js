/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_BR;
    config.coreStyles_bold = { 
        element: "b", 
        overrides: "strong" 
    };
    config.toolbar = [
        {name: "basicstyles", groups: ["basicstyles", "cleanup"], items: ["Bold", "Italic", "Underline"]},
        {name: "paragraph", groups: ["list"], items: ["NumberedList", "BulletedList"]}
    ];
};
