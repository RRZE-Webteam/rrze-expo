"use strict";

jQuery(document).ready(function ($) {

    var boothTemplateSelect = $('select#rrze-expo-booth-template');
    var boothTemplate = $('option:selected',boothTemplateSelect).val();
    $("div[class*='cmb2-id-rrze-expo-booth-decoration-template']").slideUp();
    $('div.cmb2-id-rrze-expo-booth-decoration-template'+boothTemplate).slideDown();

    boothTemplateSelect.change(function(){
        var boothTemplate = $('option:selected',this).val();
        $("div[class*='cmb2-id-rrze-expo-booth-decoration-template']").slideUp();
        $('div.cmb2-id-rrze-expo-booth-decoration-template'+boothTemplate).slideDown();
    });
});
