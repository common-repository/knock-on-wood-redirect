(window.webpackJsonp=window.webpackJsonp||[]).push([[5],{14:function(e,t,a){"use strict";a.d(t,"c",(function(){return i})),a.d(t,"b",(function(){return u})),a.d(t,"a",(function(){return d})),a.d(t,"e",(function(){return s}));var n=a(16),l=a.n(n);const{api_nonce:c,api_url:r,site_url:s}=window.wpr_object;var o,i;function u(){o&&o(),i=new n.CancelToken((function(e){o=e}))}i=new n.CancelToken((function(e){o=e}));const d=l.a.create({baseURL:r,headers:{"X-WP-Nonce":c}});t.d=l.a},15:function(e,t,a){"use strict";a.d(t,"a",(function(){return s}));var n=a(0),l=a.n(n),c=a(51);function r(){return(r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}function s(e){const{value:t,onChange:a,choices:s,readOnly:o,className:i}=e,[u,d]=Object(n.useState)(null),[m,p]=Object(n.useState)(null),{styles:b,attributes:f,update:E}=Object(c.a)(u,m),[g,h]=Object(n.useState)(!1);return l.a.createElement("div",{className:"dropdown",style:{width:"-webkit-fill-available"}},l.a.createElement("button",{disabled:o,ref:d,className:"btn btn-secondary dropdown-toggle "+i,type:"button",onClick:()=>{E(),setTimeout((function(){h(!0)}),100)},onBlur:()=>{setTimeout((function(){h(!1)}),100)}},t),l.a.createElement("div",r({className:"dropdown-menu",hidden:!g,ref:p,style:b.popper},f.popper),s.map((e,t)=>l.a.createElement("a",{key:t,className:"dropdown-item",onClick:()=>{a(e)},href:"#"},e))))}},49:function(e,t,a){"use strict";a.r(t),a.d(t,"default",(function(){return g}));var n,l,c,r,s=a(0),o=a.n(s),i=a(14),u=a(1),d=a(15);function m(e,t,a,s){l=t,c=a,r=s,(n=e)(null)}function p(e,t,a){const[l,r]=a;i.b&&Object(i.b)(),i.a.post("/get_redirects",{query:e,page:t,sortby:l,sortdir:r},{cancelToken:i.c}).then(e=>{const{results:t,count:a}=e.data;console.log(e.data),n(t),c(a)}).catch(e=>{if(i.d.isCancel(e))console.log("cancel");else{if(e.response){const{data:t,status:a,headers:n}=e.response;console.log(t,a,n)}console.log(e)}})}function b(e){l(e.target.value),n(null)}function f(e){const{RedirectData:t,index:a,onChange:n}=e,{id:l,created:c,from_url:r,to_url:m,is_disabled:p,redir_type:b,hits:f,last_hit:E}=t,[g,h]=Object(s.useState)(!1),[v,N]=Object(s.useState)(!1),[y,C]=Object(s.useState)(null),[k,O]=Object(s.useState)(null),[_,j]=Object(s.useState)(null),w=[[y],[k],[_,()=>{j(null)}]].filter(e=>null!=e[0]),[S,T]=Object(s.useState)(r),[x,R]=Object(s.useState)(m),[F,D]=Object(s.useState)(b);0!=l||g||h(!0);const A=e=>{0!=l?(T(r),R(m),D(b),C(null),O(null),h(!g)):n(null,a)},B=e=>!!/^[ -~]+$/.test(e)||"Url path contains non-ascii characters!";let W=g?"table-primary":1==p?"table-danger":"";return o.a.createElement(o.a.Fragment,null,o.a.createElement("tr",{hidden:0==w.length},o.a.createElement("td",{colSpan:"7",className:"alert alert-danger",role:"alert"},w.map((e,t)=>{const[a,n]=e;return o.a.createElement("p",{key:t},a,n&&o.a.createElement("button",{className:"btn btn-danger btn-sm float-right",onClick:n},o.a.createElement("div",{className:"fas fa-times"})))}))),o.a.createElement("tr",{key:l,className:W},o.a.createElement("th",{scope:"col"},0==l?o.a.createElement("div",{className:"fas fa-plus"}):l),o.a.createElement("td",null,o.a.createElement("div",{className:"input-group input-group-sm"},o.a.createElement("input",{readOnly:!g||l>0,className:"form-control",type:"text",placeholder:r,value:g&&0==l?S:"",onChange:e=>{let t=e.target.value.replace(/\s/g,"");t.startsWith("/")||(t="/"+t);let a=B(t);C(1!=a?a:null),T(t)},onFocus:e=>{e.target.select()}}),o.a.createElement("div",{className:"input-group-append"},o.a.createElement("button",{disabled:g||1==p,className:"btn btn-success",onClick:function(){window.open(i.e+r)}},o.a.createElement("div",{className:"fas fa-link"})))),o.a.createElement("input",{readOnly:!g,className:"form-control form-control-sm",type:"text",placeholder:m,value:g?x:"",onChange:e=>{let t=e.target.value.replace(/\s/g,"");t.startsWith("/")||(t="/"+t);let a=B(t);O(1!=a?a:null),R(t)},onFocus:e=>{e.target.select()}})),o.a.createElement("td",null,o.a.createElement(d.a,{readOnly:!g,value:F,choices:[301,302],onChange:e=>{D(e)}})),o.a.createElement("td",null,f),o.a.createElement("td",null,E||"Never"),o.a.createElement("td",null,c),o.a.createElement("td",{align:"right"},o.a.createElement("div",{hidden:g||v,className:"btn-group btn-group-sm redirect-button-group"},o.a.createElement("button",{className:"btn btn-warning btn-sm",onClick:e=>{N(!0),i.a.post("/set_disabled",{id:l,is_disabled:1==p?0:1},{cancelToken:i.c}).then(e=>{n(e.data,a),N(!1)}).catch(e=>{i.d.isCancel(e)?console.log("cancel",e):(console.log(e),j(e.response.data.code),N(!1))})}},1==p?"Enable":"Disable"),o.a.createElement("button",{className:"btn btn-info btn-sm",onClick:A},"Edit"),o.a.createElement(d.a,{className:"btn-danger btn-sm delete-confirm",value:"Delete",choices:["Confirm"],onChange:e=>{N(!0),i.a.post("/remove_redirect",{id:l},{cancelToken:i.c}).then(e=>{n(null,a)}).catch(e=>{i.d.isCancel(e)?console.log("cancel",e):(console.log(e),j(e.response.data.code),N(!1))})}})),o.a.createElement("div",{hidden:!g||v,className:"btn-group btn-group-sm redirect-button-group"},o.a.createElement("button",{className:"btn btn-danger btn-sm",onClick:A},"Cancel"),o.a.createElement("button",{disabled:w.length>0,className:"btn btn-success btn-sm",onClick:e=>{N(!0),i.a.post("/set_redirect",{id:l,to_url:x,from_url:S,redir_type:F},{cancelToken:i.c}).then(e=>{n(e.data,a),h(!1),N(!1)}).catch(e=>{i.d.isCancel(e)?console.log("cancel",e):(j(e.response.data.code),N(!1))})}},"Save")),o.a.createElement("div",{hidden:!v},o.a.createElement(u.a,{fontSize:"25px"})))))}function E(e){const{data:t,onChange:a,orderBy:l}=e,[c,s]=l,i=(e,n)=>{let l=[...t];null!=e?l.splice(n,1,e):l.splice(n,1),a(l)};return o.a.createElement("table",{className:"table"},o.a.createElement("thead",null,o.a.createElement("tr",null,Object.entries({id:"#",from_url:"From & To",redir_type:"Type",hits:"Hits",last_hit:"Last Hit",created:"Created",is_disabled:"Actions"}).map((e,t)=>{const[a,l]=e;var i=[];return a==c&&(i.push("fas"),"asc"==s?i.push("fa-sort-up"):i.push("fa-sort-down")),o.a.createElement("th",{scope:"col",onClick:()=>{var e,t;t="asc",(e=a)==c&&(t="asc"==s?"desc":"asc"),r([e,t]),n(null)},className:a,key:t},l," ",o.a.createElement("div",{className:i.join(" ")})," ")}))),o.a.createElement("tbody",null,t.map((e,t)=>o.a.createElement(f,{onChange:i,RedirectData:e,key:e.id,index:t}))))}function g(e){const{}=e,[t,a]=Object(s.useState)(void 0),[n,l]=Object(s.useState)(0),[c,r]=Object(s.useState)(""),[i,d]=Object(s.useState)(["id","asc"]),[f,g]=Object(s.useState)(0);void 0===t?m(a,r,l,d):null===t&&p(c,f,i);const h=e=>{let n=Number(e);if(Number.isInteger(n)&&n>0){if(f+1<n&&t.length<8)return;g(n-1),a(null)}};return o.a.createElement("div",null,o.a.createElement("div",{id:"search-bar"},o.a.createElement("div",{className:"input-group"},o.a.createElement("input",{type:"text",className:"form-control",onChange:b,value:c}),o.a.createElement("div",{className:"input-group-append"},o.a.createElement("button",{disabled:t&&t.length>0&&0==t[0].id,className:"btn btn-success",onClick:function(){a([{id:0,from_url:"",to_url:"",is_disabled:0,redir_type:301},...t])}},o.a.createElement("div",{className:"fas fa-plus"})),o.a.createElement("button",{className:"btn btn-info",onClick:function(){a(null)}},o.a.createElement("div",{className:"fas fa-sync"}))))),o.a.createElement("div",{className:"container-fluid"},t?Array.isArray(t)?o.a.createElement(E,{onChange:function(e){a(e)},data:t,orderBy:i}):o.a.createElement("p",null,"ERROR!"):o.a.createElement(u.a,{fontSize:"6rem"})),o.a.createElement("ul",{className:"table-footer"},o.a.createElement("li",null,o.a.createElement("div",{className:"input-group"},o.a.createElement("div",{className:"input-group-prepend"},o.a.createElement("button",{className:"btn btn-secondary",onClick:()=>{h(f)}},o.a.createElement("div",{className:"fas fa-caret-left"}))),o.a.createElement("input",{className:"form-control table-footer-page",type:"text",placeholder:f+1,value:f+1,onChange:e=>{h(e.target.value)},onFocus:e=>{e.target.select()}}),o.a.createElement("div",{className:"input-group-append"},o.a.createElement("button",{className:"btn btn-secondary",onClick:()=>{h(f+2)}},o.a.createElement("div",{className:"fas fa-caret-right"}))))),o.a.createElement("li",null,o.a.createElement("p",null,n," total"))))}}}]);