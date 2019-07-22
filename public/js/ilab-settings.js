!function(e,t){"use strict";"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?module.exports=t():e.MediaBox=t()}(this,function(){"use strict";var e=function(t,o){o=o||0;return this&&this instanceof e?!!t&&(this.params=Object.assign({autoplay:"1"},o),this.selector=t instanceof NodeList?t:document.querySelectorAll(t),this.root=document.querySelector("body"),void this.run()):new e(t,o)};return e.prototype={run:function(){Array.prototype.forEach.call(this.selector,function(e){e.addEventListener("click",function(t){t.preventDefault();var o=this.parseUrl(e.getAttribute("href"));this.render(o),this.events()}.bind(this),!1)}.bind(this)),this.root.addEventListener("keyup",function(e){27===(e.keyCode||e.which)&&this.close(this.root.querySelector(".mediabox-wrap"))}.bind(this),!1)},template:function(e,t){var o;for(o in t)t.hasOwnProperty(o)&&(e=e.replace(new RegExp("{"+o+"}","g"),t[o]));return e},parseUrl:function(e){var t,o={};return(t=e.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/))?(o.provider="youtube",o.id=t[2]):(t=e.match(/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/))?(o.provider="vimeo",o.id=t[3]):(o.provider="Unknown",o.id=""),o},render:function(e){var t,o,n;if("youtube"===e.provider)t="https://www.youtube.com/embed/"+e.id;else{if("vimeo"!==e.provider)throw new Error("Invalid video URL");t="https://player.vimeo.com/video/"+e.id}n=this.serialize(this.params),o=this.template('<div class="mediabox-wrap" role="dialog" aria-hidden="false"><div class="mediabox-content" role="document" tabindex="0"><span id="mediabox-esc" class="mediabox-close" aria-label="close" tabindex="1"></span><iframe src="{embed}{params}" frameborder="0" allowfullscreen></iframe></div></div>',{embed:t,params:n}),this.lastFocusElement=document.activeElement,this.root.insertAdjacentHTML("beforeend",o),document.body.classList.add("stop-scroll")},events:function(){var e=document.querySelector(".mediabox-wrap"),t=document.querySelector(".mediabox-content");e.addEventListener("click",function(t){(t.target&&"SPAN"===t.target.nodeName&&"mediabox-close"===t.target.className||"DIV"===t.target.nodeName&&"mediabox-wrap"===t.target.className||"mediabox-content"===t.target.className&&"IFRAME"!==t.target.nodeName)&&this.close(e)}.bind(this),!1),document.addEventListener("focus",function(e){t&&!t.contains(e.target)&&(e.stopPropagation(),t.focus())},!0),t.addEventListener("keypress",function(t){13===t.keyCode&&this.close(e)}.bind(this),!1)},close:function(e){if(null===e)return!0;var t=null;t&&clearTimeout(t),e.classList.add("mediabox-hide"),t=setTimeout(function(){var e=document.querySelector(".mediabox-wrap");null!==e&&(document.body.classList.remove("stop-scroll"),this.root.removeChild(e),this.lastFocusElement.focus())}.bind(this),500)},serialize:function(e){return"?"+Object.keys(e).reduce(function(t,o){return t.push(o+"="+encodeURIComponent(e[o])),t},[]).join("&")}},e}),"function"!=typeof Object.assign&&Object.defineProperty(Object,"assign",{value:function(e,t){"use strict";if(null==e)throw new TypeError("Cannot convert undefined or null to object");for(var o=Object(e),n=1;n<arguments.length;n++){var i=arguments[n];if(null!=i)for(var r in i)Object.prototype.hasOwnProperty.call(i,r)&&(o[r]=i[r])}return o},writable:!0,configurable:!0}),jQuery(document).ready(function(e){var t=e(".ilab-notification-container");t&&t.length>0&&(e(".update-nag").each(function(){e(this).css({"margin-top":0,"margin-bottom":"15px"}),e(this).appendTo(t)}),e(".notice").each(function(){e(this).appendTo(t)}),e(".fs-notice").each(function(){e(this).appendTo(t)})),e(".upgrade-close").on("click",function(t){return t.preventDefault(),e.post(ajaxurl,{action:"ilab_hide_upgrade_bug"},function(e){}),e(".upgrade-promo").addClass("hide-on-mobile"),!1}),MediaBox(".mediabox")});