(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{14:function(e,t,n){"use strict";n.d(t,"c",(function(){return i})),n.d(t,"b",(function(){return u})),n.d(t,"a",(function(){return d})),n.d(t,"e",(function(){return c}));var a=n(16),r=n.n(a);const{api_nonce:o,api_url:s,site_url:c}=window.wpr_object;var l,i;function u(){l&&l(),i=new a.CancelToken((function(e){l=e}))}i=new a.CancelToken((function(e){l=e}));const d=r.a.create({baseURL:s,headers:{"X-WP-Nonce":o}});t.d=r.a},15:function(e,t,n){"use strict";n.d(t,"a",(function(){return c}));var a=n(0),r=n.n(a),o=n(51);function s(){return(s=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var a in n)Object.prototype.hasOwnProperty.call(n,a)&&(e[a]=n[a])}return e}).apply(this,arguments)}function c(e){const{value:t,onChange:n,choices:c,readOnly:l,className:i}=e,[u,d]=Object(a.useState)(null),[p,m]=Object(a.useState)(null),{styles:b,attributes:f,update:g}=Object(o.a)(u,p),[w,h]=Object(a.useState)(!1);return r.a.createElement("div",{className:"dropdown",style:{width:"-webkit-fill-available"}},r.a.createElement("button",{disabled:l,ref:d,className:"btn btn-secondary dropdown-toggle "+i,type:"button",onClick:()=>{g(),setTimeout((function(){h(!0)}),100)},onBlur:()=>{setTimeout((function(){h(!1)}),100)}},t),r.a.createElement("div",s({className:"dropdown-menu",hidden:!w,ref:m,style:b.popper},f.popper),c.map((e,t)=>r.a.createElement("a",{key:t,className:"dropdown-item",onClick:()=>{n(e)},href:"#"},e))))}},46:function(e,t,n){"use strict";n.r(t),n.d(t,"default",(function(){return l}));var a=n(0),r=n.n(a),o=n(14),s=n(15);function c(e){const{now:t,max:n}=e;let a=Math.round(t/n*100)+"%";return r.a.createElement("div",{className:"progress"},r.a.createElement("div",{className:"progress-bar progress-bar-striped progress-bar-animated",role:"progressbar",style:{width:a}}))}function l(e){const{setPage:t}=e,[n,l]=Object(a.useState)(!1),[i,u]=Object(a.useState)(0),[d,p]=Object(a.useState)(void 0);function m(){u(e=>e+1)}return Object(a.useEffect)(()=>{},[]),r.a.createElement("div",null,r.a.createElement("h3",{style:{paddingTop:"3rem"}},"Upgrading is a risky process."),r.a.createElement("p",null,"Please ensure you have a database backup ",r.a.createElement("span",{style:{fontWeight:"bold"}},"before")," proceeding."),r.a.createElement("div",{hidden:null==d},d),r.a.createElement("br",null),n&&r.a.createElement(r.a.Fragment,null,r.a.createElement(c,{now:i,max:i+1}),r.a.createElement("br",null)),r.a.createElement(s.a,{className:"btn-success btn-confirm",readOnly:n,value:"Continue",choices:["Yes, I'm sure"],onChange:async function(){l(!0),u(.25);let e=setInterval(m,500);await new Promise(e=>setTimeout(e,900));let t=!1;try{await o.a.post("/do_database_upgrade"),t=!0}catch(e){if(o.d.isCancel(e))console.log("cancel");else{if(e.response){const{data:t,status:n,headers:a}=e.response;console.log(t,n,a)}console.log(e),p(r.a.createElement("p",{className:"text-danger"},"Sorry, something went wrong when trying to update app state. ",r.a.createElement("br",null),"  Please ",r.a.createElement("strong",null,"try again")," or ",r.a.createElement("strong",null,"contact support"),"."))}}clearInterval(e),u(1e3),await new Promise(e=>setTimeout(e,900)),1==t?location.reload():l(!1)}}))}}}]);