"use strict";!function(n){n.isNumeric=function(n){return/^[0-9.,]/.test(n)},n.fn.maskInput=function(){var t=n(this).val().split(""),i=[];console.log(t),t.forEach(function(t){console.log(t),n.isNumeric(t)&&i.push(t)});var o=i.join("");n(this).val(o)},n(document).ready(function(){n("#diy-project-meta-cost").on("input",function(t){n(this).maskInput()})})}(jQuery);