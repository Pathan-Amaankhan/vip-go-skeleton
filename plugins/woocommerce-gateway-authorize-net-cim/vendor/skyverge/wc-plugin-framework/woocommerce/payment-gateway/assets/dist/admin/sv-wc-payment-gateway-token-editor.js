parcelRequire=function(e,r,t,n){var i,o="function"==typeof parcelRequire&&parcelRequire,u="function"==typeof require&&require;function f(t,n){if(!r[t]){if(!e[t]){var i="function"==typeof parcelRequire&&parcelRequire;if(!n&&i)return i(t,!0);if(o)return o(t,!0);if(u&&"string"==typeof t)return u(t);var c=new Error("Cannot find module '"+t+"'");throw c.code="MODULE_NOT_FOUND",c}p.resolve=function(r){return e[t][1][r]||r},p.cache={};var l=r[t]=new f.Module(t);e[t][0].call(l.exports,p,l,l.exports,this)}return r[t].exports;function p(e){return f(p.resolve(e))}}f.isParcelRequire=!0,f.Module=function(e){this.id=e,this.bundle=f,this.exports={}},f.modules=e,f.cache=r,f.parent=o,f.register=function(r,t){e[r]=[function(e,r){r.exports=t},{}]};for(var c=0;c<t.length;c++)try{f(t[c])}catch(e){i||(i=e)}if(t.length){var l=f(t[t.length-1]);"object"==typeof exports&&"undefined"!=typeof module?module.exports=l:"function"==typeof define&&define.amd?define(function(){return l}):n&&(this[n]=l)}if(parcelRequire=f,i)throw i;return f}({"YLvv":[function(require,module,exports) {
(function(){jQuery(function(t){"use strict";var e,n,a;return a=null!=(n=window.wc_payment_gateway_token_editor)?n:{},t(".sv_wc_payment_gateway_token_editor").each(function(){return 0===t(this).find("tr.token").length?t(this).find("tr.no-tokens").show():t(this).find("tr.no-tokens").hide()}),t(".sv_wc_payment_gateway_token_editor").on("click",'.button[data-action="remove"]',function(n){var o,r,i;if(n.preventDefault(),confirm(a.actions.remove_token.ays))return(r=t(this).closest("table")).block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),r.find(".error").remove(),(i=t(this).closest("tr")).hasClass("new-token")?(r.unblock(),i.remove()):(o={action:"wc_payment_gateway_"+r.data("gateway-id")+"_admin_remove_payment_token",user_id:t(this).data("user-id"),token_id:t(this).data("token-id"),security:a.actions.remove_token.nonce},t.post(a.ajax_url,o).done(function(n){return n.success?(t(i).remove(),0===r.find("tr.token").length?r.find("tr.no-tokens").show():void 0):e(r,n.data)}).fail(function(t,n,a){return e(r,n+": "+a)}).always(function(){return r.unblock()}))}),t("table.sv_wc_payment_gateway_token_editor").on("click",'.button[data-action="add-new"]',function(e){var n,o,r,i;return e.preventDefault(),(i=t(this).closest("table")).block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),o=(n=i.find("tbody.tokens")).find("tr.token").length,r={action:"wc_payment_gateway_"+i.data("gateway-id")+"_admin_get_blank_payment_token",index:o+1,security:a.actions.add_token.nonce},t.post(a.ajax_url,r,function(t){return!0===t.success&&n.append(t.data),i.find("tr.no-tokens").hide(),i.unblock()})}),t("table.sv_wc_payment_gateway_token_editor").on("click",'.button[data-action="refresh"]',function(n){var o,r,i;return n.preventDefault(),(i=t(this).closest("table")).block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),i.find(".error").remove(),(o=i.find("tbody.tokens")).find("tr.token").length,r={action:"wc_payment_gateway_"+i.data("gateway-id")+"_admin_refresh_payment_tokens",user_id:t(this).data("user-id"),security:a.actions.refresh.nonce},t.post(a.ajax_url,r).done(function(t){return t.success?null!=t.data?(i.find("tr.no-tokens").hide(),o.html(t.data)):(o.empty(),i.find("tr.no-tokens").show()):e(i,t.data)}).fail(function(t,n,a){return e(i,n+": "+a)}).always(function(){return i.unblock()})}),t("table.sv_wc_payment_gateway_token_editor").on("click",'.sv-wc-payment-gateway-token-editor-action-button[data-action="save"]',function(e){var n,o,r,i;return o=t(this).closest("table"),n=o.find("tfoot th"),o.block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),n.find(".error, .success").remove(),i=o.find('tbody.tokens tr.token input[type="text"]'),r=!1,i.each(function(i){var s,c,d;if(t(this).removeClass("error"),d=t(this).val(),c=t(this).prop("required"),s=t(this).attr("pattern"),c||d)return!d.match(s)||c&&!d?(e.preventDefault(),t(this).addClass("error"),r||(n.prepend('<span class="error">'+a.actions.save.error+"</span>"),t(this).focus(),r=!0),o.unblock()):void 0})}),e=function(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"";return console.error(e),n||(n=a.i18n.general_error),t.find("th.actions").prepend('<span class="error">'+n+"</span>")}})}).call(this);
},{}]},{},["YLvv"], null)
//# sourceMappingURL=../admin/sv-wc-payment-gateway-token-editor.js.map