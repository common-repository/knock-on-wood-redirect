(window.webpackJsonp=window.webpackJsonp||[]).push([[4],{14:function(e,t,n){"use strict";n.d(t,"c",(function(){return u})),n.d(t,"b",(function(){return i})),n.d(t,"a",(function(){return d})),n.d(t,"e",(function(){return l}));var a=n(16),o=n.n(a);const{api_nonce:c,api_url:r,site_url:l}=window.wpr_object;var s,u;function i(){s&&s(),u=new a.CancelToken((function(e){s=e}))}u=new a.CancelToken((function(e){s=e}));const d=o.a.create({baseURL:r,headers:{"X-WP-Nonce":c}});t.d=o.a},15:function(e,t,n){"use strict";n.d(t,"a",(function(){return l}));var a=n(0),o=n.n(a),c=n(51);function r(){return(r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var a in n)Object.prototype.hasOwnProperty.call(n,a)&&(e[a]=n[a])}return e}).apply(this,arguments)}function l(e){const{value:t,onChange:n,choices:l,readOnly:s,className:u}=e,[i,d]=Object(a.useState)(null),[p,f]=Object(a.useState)(null),{styles:b,attributes:m,update:w}=Object(c.a)(i,p),[h,y]=Object(a.useState)(!1);return o.a.createElement("div",{className:"dropdown",style:{width:"-webkit-fill-available"}},o.a.createElement("button",{disabled:s,ref:d,className:"btn btn-secondary dropdown-toggle "+u,type:"button",onClick:()=>{w(),setTimeout((function(){y(!0)}),100)},onBlur:()=>{setTimeout((function(){y(!1)}),100)}},t),o.a.createElement("div",r({className:"dropdown-menu",hidden:!h,ref:f,style:b.popper},m.popper),l.map((e,t)=>o.a.createElement("a",{key:t,className:"dropdown-item",onClick:()=>{n(e)},href:"#"},e))))}},45:function(e,t,n){"use strict";n.r(t),n.d(t,"default",(function(){return l}));var a=n(0),o=n.n(a),c=n(14);n(15);function r(){location.reload()}function l(e){const{setPage:t}=e,[n,l]=Object(a.useState)(!1),[s,u]=Object(a.useState)(void 0);return Object(a.useEffect)(()=>{!async function(){try{await c.a.post("/skip_database_upgrade"),l(!0)}catch(e){console.log(e),u(o.a.createElement("p",{className:"text-danger"},"Sorry, something went wrong when trying to update app state. ",o.a.createElement("br",null),"  Please ",o.a.createElement("strong",null,"reload and try again")," or ",o.a.createElement("strong",null,"contact support"),".")),l(!1)}}()},[]),o.a.createElement("div",null,o.a.createElement("h3",null,"Thank you for downloading Knock on Wood Redirect!"),o.a.createElement("div",{hidden:null==s},s),o.a.createElement("button",{disabled:!n,onClick:r,className:"btn "+(n?"btn-success":"btn-outline-secondary")},"Finish"))}}}]);