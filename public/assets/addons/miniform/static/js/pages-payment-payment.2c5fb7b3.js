(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-payment-payment"],{"0fe9":function(t,e,i){"use strict";i("a9e3"),i("ac1f"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={name:"u-skeleton",props:{elColor:{type:String,default:"#e5e5e5"},bgColor:{type:String,default:"#ffffff"},animation:{type:Boolean,default:!1},borderRadius:{type:[String,Number],default:"10"},loading:{type:Boolean,default:!0}},data:function(){return{windowWinth:750,windowHeight:1500,filletNodes:[],circleNodes:[],RectNodes:[],top:0,left:0}},methods:{selecterQueryInfo:function(){var t=this,e="";e=uni.createSelectorQuery(),e.selectAll(".u-skeleton").boundingClientRect().exec((function(e){t.windowHeight=e[0][0].height,t.windowWinth=e[0][0].width,t.top=e[0][0].bottom-e[0][0].height,t.left=e[0][0].left})),this.getRectEls(),this.getCircleEls(),this.getFilletEls()},getRectEls:function(){var t=this,e="";e=uni.createSelectorQuery(),e.selectAll(".u-skeleton-rect").boundingClientRect().exec((function(e){t.RectNodes=e[0]}))},getFilletEls:function(){var t=this,e="";e=uni.createSelectorQuery(),e.selectAll(".u-skeleton-fillet").boundingClientRect().exec((function(e){t.filletNodes=e[0]}))},getCircleEls:function(){var t=this,e="";e=uni.createSelectorQuery(),e.selectAll(".u-skeleton-circle").boundingClientRect().exec((function(e){t.circleNodes=e[0]}))}},mounted:function(){var t=uni.getSystemInfoSync();this.windowHeight=t.windowHeight,this.windowWinth=t.windowWidth,this.selecterQueryInfo()}};e.default=n},1410:function(t,e,i){"use strict";i.r(e);var n=i("44dc"),a=i("a729");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("6dbf"),i("d874");var r,c=i("f0c5"),u=Object(c["a"])(a["default"],n["b"],n["c"],!1,null,"4b1c0cb0",null,!1,n["a"],r);e["default"]=u.exports},"1c6b":function(t,e,i){var n=i("d3e3");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("623924cc",n,!0,{sourceMap:!1,shadowMode:!1})},"44dc":function(t,e,i){"use strict";i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));var n={faNavbar:i("0bf6").default,uCellGroup:i("75aa").default,uRadioGroup:i("47bf").default,uCellItem:i("aa09").default,uRadio:i("2604").default,uButton:i("33b4").default,uSkeleton:i("e65f").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"u-skeleton"},[i("fa-navbar",{attrs:{title:"订单支付","border-bottom":!1}}),i("v-uni-view",{staticClass:"u-p-30 u-flex bg-white"},[i("v-uni-image",{staticClass:"thumb u-skeleton-rect",attrs:{src:t.order.project&&t.order.project.image}}),i("v-uni-view",{staticClass:"u-m-l-20 order"},[i("v-uni-view",{staticClass:"u-skeleton-rect",domProps:{textContent:t._s(t.order.project&&t.order.project.title)}}),i("v-uni-view",{staticClass:"price u-skeleton-rect"},[t._v("￥"+t._s(t.order.amount))])],1)],1),i("v-uni-view",{staticClass:"bg-white u-m-t-30"},[i("u-cell-group",{attrs:{title:"支付方式"}},[i("u-radio-group",{on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.radioGroupChange.apply(void 0,arguments)}},model:{value:t.paytype,callback:function(e){t.paytype=e},expression:"paytype"}},[i("v-uni-view",{staticClass:"paytype"},[i("u-cell-item",{attrs:{"icon-style":{color:"#20D029"},arrow:!1,icon:"weixin-circle-fill",title:"微信支付","hover-class":"cell-hover-class"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.paytype="wechat"}}},[i("u-radio",{attrs:{slot:"right-icon",name:"wechat","active-color":t.theme.bgColor},slot:"right-icon"})],1),i("u-cell-item",{attrs:{"icon-style":{color:"#00A1E9"},arrow:!1,icon:"zhifubao-circle-fill",title:"支付宝支付","hover-class":"cell-hover-class"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.paytype="alipay"}}},[i("u-radio",{attrs:{slot:"right-icon",name:"alipay","active-color":t.theme.bgColor},slot:"right-icon"})],1)],1)],1)],1)],1),i("v-uni-view",{staticClass:"payment bg-white"},[i("u-button",{attrs:{type:"primary","hover-class":"none","custom-style":{backgroundColor:t.theme.bgColor,color:t.theme.color},shape:"circle"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.submit.apply(void 0,arguments)}}},[t._v("立即支付")])],1),i("u-skeleton",{attrs:{loading:t.loading,animation:!0,bgColor:"#FFF"}})],1)},o=[]},"4c83":function(t,e,i){"use strict";var n=i("4ea4");i("c975"),i("d3b7"),i("ac1f"),i("25f0"),i("466d"),i("5319"),i("1276"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var a=n(i("1da1")),o=i("b7a0"),r={mixins:[o.loginfunc],onLoad:function(t){this.id=t.id||"",this.orderid=t.orderid||"",this.getOrder()},mounted:function(){var t=window.location.href;this.$util.isWeiXinBrowser()&&-1!=t.indexOf("#")&&!t.match(/\?#/)&&location.replace(window.location.href.split("#")[0]+"?"+window.location.hash)},data:function(){return{paytype:"wechat",id:"",orderid:"",order:{},loading:!0}},methods:{radioGroupChange:function(t){this.paytype=t},getOrder:function(){var t=this;this.$api.order({id:this.id,orderid:this.orderid}).then((function(e){e.code?(t.loading=!1,t.order=e.data):(t.$u.toast(e.msg),setTimeout((function(){e.data.project_id?t.$u.route("/pages/my/items"):uni.navigateBack({delta:1})}),1e3))}))},submit:function(){var t=(0,a.default)(regeneratorRuntime.mark((function t(){var e,i,n,a,o,r=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e={id:this.id,orderid:this.orderid,paytype:this.paytype,method:"wap"},!this.$util.isWeiXinBrowser()||"wechat"!=this.paytype){t.next=16;break}return e.method="mp",t.next=5,this.$api.payment(e);case 5:if(i=t.sent,"bind"!=i.data){t.next=9;break}return this.goAuth(),t.abrupt("return");case 9:if(i.code){t.next=13;break}return this.$u.toast(i.msg),t.abrupt("return");case 13:window.WeixinJSBridge.invoke("getBrandWCPayRequest",{appId:i.data.appId,timeStamp:i.data.timeStamp,nonceStr:i.data.nonceStr,package:i.data.package,signType:i.data.signType,paySign:i.data.paySign},(function(t){"get_brand_wcpay_request:ok"===t.err_msg?(r.$u.toast("支付成功！"),r.$u.route("/pages/my/items")):"get_brand_wcpay_request:cancel"===t.err_msg?r.$u.toast("取消支付"):r.$u.toast("支付失败")})),t.next=35;break;case 16:return e.returnurl=window.location.href,t.next=19,this.$api.payment(e);case 19:if(n=t.sent,n.code){t.next=23;break}return this.$u.toast(n.msg),t.abrupt("return");case 23:if(!n.data.toString().match(/^((?:[a-z]+:)?\/\/)(.*)/i)){t.next=26;break}return location.href=n.data,t.abrupt("return");case 26:if(document.getElementsByTagName("body")[0].innerHTML=n.data,a=document.querySelector("form"),!(a&&a.length>0)){t.next=31;break}return a.submit(),t.abrupt("return");case 31:if(o=document.querySelector('meta[http-equiv="refresh"]'),!(o&&o.length>0)){t.next=35;break}return setTimeout((function(){location.href=o.content.split(/;/)[1]}),300),t.abrupt("return");case 35:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}()}};e.default=r},5143:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \n */uni-page-body[data-v-4b1c0cb0]{background-color:#f4f6f8}body.?%PAGE?%[data-v-4b1c0cb0]{background-color:#f4f6f8}',""]),t.exports=e},6864:function(t,e,i){"use strict";var n;i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return t.loading?i("v-uni-view",{style:{width:t.windowWinth+"px",height:t.windowHeight+"px",backgroundColor:t.bgColor,position:"absolute",left:t.left+"px",top:t.top+"px",zIndex:9998,overflow:"hidden"},on:{touchmove:function(e){e.stopPropagation(),e.preventDefault(),arguments[0]=e=t.$handleEvent(e)}}},[t._l(t.RectNodes,(function(e,n){return i("v-uni-view",{key:t.$u.guid(),class:[t.animation?"skeleton-fade":""],style:{width:e.width+"px",height:e.height+"px",backgroundColor:t.elColor,position:"absolute",left:e.left-t.left+"px",top:e.top-t.top+"px"}})})),t._l(t.circleNodes,(function(e,n){return i("v-uni-view",{key:t.$u.guid(),class:t.animation?"skeleton-fade":"",style:{width:e.width+"px",height:e.height+"px",backgroundColor:t.elColor,borderRadius:e.width/2+"px",position:"absolute",left:e.left-t.left+"px",top:e.top-t.top+"px"}})})),t._l(t.filletNodes,(function(e,n){return i("v-uni-view",{key:t.$u.guid(),class:t.animation?"skeleton-fade":"",style:{width:e.width+"px",height:e.height+"px",backgroundColor:t.elColor,borderRadius:t.borderRadius+"rpx",position:"absolute",left:e.left-t.left+"px",top:e.top-t.top+"px"}})}))],2):t._e()},o=[]},"6dbf":function(t,e,i){"use strict";var n=i("72a0"),a=i.n(n);a.a},"72a0":function(t,e,i){var n=i("5143");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("3274f52f",n,!0,{sourceMap:!1,shadowMode:!1})},"77e4":function(t,e,i){var n=i("b926");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("05a6d020",n,!0,{sourceMap:!1,shadowMode:!1})},"7c62":function(t,e,i){"use strict";i.r(e);var n=i("0fe9"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},a729:function(t,e,i){"use strict";i.r(e);var n=i("4c83"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},b926:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \n */.thumb[data-v-4b1c0cb0]{width:%?150?%;height:%?150?%;border-radius:%?10?%}.order[data-v-4b1c0cb0]{height:%?150?%;padding:%?10?% 0;display:flex;flex-direction:column;justify-content:space-between}.paytype[data-v-4b1c0cb0]{width:100vw}.payment[data-v-4b1c0cb0]{position:fixed;bottom:0;left:0;width:100%;padding:%?30?% %?80?%}',""]),t.exports=e},b9e3:function(t,e,i){"use strict";var n=i("1c6b"),a=i.n(n);a.a},d3e3:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \n */.skeleton-fade[data-v-ee51da2e]{width:100%;height:100%;background:#c2cfd6;-webkit-animation-duration:1.5s;animation-duration:1.5s;-webkit-animation-name:blink-data-v-ee51da2e;animation-name:blink-data-v-ee51da2e;-webkit-animation-timing-function:ease-in-out;animation-timing-function:ease-in-out;-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite}@-webkit-keyframes blink-data-v-ee51da2e{0%{opacity:1}50%{opacity:.4}100%{opacity:1}}@keyframes blink-data-v-ee51da2e{0%{opacity:1}50%{opacity:.4}100%{opacity:1}}',""]),t.exports=e},d874:function(t,e,i){"use strict";var n=i("77e4"),a=i.n(n);a.a},e65f:function(t,e,i){"use strict";i.r(e);var n=i("6864"),a=i("7c62");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("b9e3");var r,c=i("f0c5"),u=Object(c["a"])(a["default"],n["b"],n["c"],!1,null,"ee51da2e",null,!1,n["a"],r);e["default"]=u.exports}}]);