dispatch.to("Foundry/2.1 Core Plugins").at(function($){$.isUrl=function(s){var regexp=/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;return regexp.test(s)};var Query=function(queryString){"use strict";var parseQuery=function(q){var i,ps,p,kvp,k,v,arr=[];if("undefined"==typeof q||null===q||""===q)return arr;for(0===q.indexOf("?")&&(q=q.substring(1)),ps=q.toString().split(/[&;]/),i=0;i<ps.length;i++)p=ps[i],kvp=p.split("="),k=kvp[0],v=-1===p.indexOf("=")?null:null===kvp[1]?"":kvp[1],arr.push([k,v]);
return arr},params=parseQuery(queryString),toString=function(){var i,param,s="";for(i=0;i<params.length;i++)param=params[i],s.length>0&&(s+="&"),s+=null===param[1]?param[0]:param.join("=");return s.length>0?"?"+s:s},decode=function(s){return s=decodeURIComponent(s),s=s.replace("+"," ")},getParamValue=function(key){var param,i;for(i=0;i<params.length;i++)if(param=params[i],decode(key)===decode(param[0]))return param[1]},getParamValues=function(key){var i,param,arr=[];for(i=0;i<params.length;i++)param=params[i],decode(key)===decode(param[0])&&arr.push(param[1]);
return arr},deleteParam=function(key,val){var i,param,keyMatchesFilter,valMatchesFilter,arr=[];for(i=0;i<params.length;i++)param=params[i],keyMatchesFilter=decode(param[0])===decode(key),valMatchesFilter=decode(param[1])===decode(val),(1===arguments.length&&!keyMatchesFilter||2===arguments.length&&!keyMatchesFilter&&!valMatchesFilter)&&arr.push(param);return params=arr,this},addParam=function(key,val,index){return 3===arguments.length&&-1!==index?(index=Math.min(index,params.length),params.splice(index,0,[key,val])):arguments.length>0&&params.push([key,val]),this
},replaceParam=function(key,newVal,oldVal){var i,param,index=-1;if(3===arguments.length){for(i=0;i<params.length;i++)if(param=params[i],decode(param[0])===decode(key)&&decodeURIComponent(param[1])===decode(oldVal)){index=i;break}deleteParam(key,oldVal).addParam(key,newVal,index)}else{for(i=0;i<params.length;i++)if(param=params[i],decode(param[0])===decode(key)){index=i;break}deleteParam(key),addParam(key,newVal,index)}return this};return{getParamValue:getParamValue,getParamValues:getParamValues,deleteParam:deleteParam,addParam:addParam,replaceParam:replaceParam,toString:toString}
},Uri=function(uriString){"use strict";var strictMode=!1,parseUri=function(str){for(var parsers={strict:/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,loose:/^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/},keys=["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],q={name:"queryKey",parser:/(?:^|&)([^&=]*)=?([^&]*)/g},m=parsers[strictMode?"strict":"loose"].exec(str),uri={},i=14;i--;)uri[keys[i]]=m[i]||"";
return uri[q.name]={},uri[keys[12]].replace(q.parser,function($0,$1,$2){$1&&(uri[q.name][$1]=$2)}),uri},uriParts=parseUri(uriString||""),queryObj=new Query(uriParts.query),protocol=function(val){return"undefined"!=typeof val&&(uriParts.protocol=val),uriParts.protocol},hasAuthorityPrefixUserPref=null,hasAuthorityPrefix=function(val){return"undefined"!=typeof val&&(hasAuthorityPrefixUserPref=val),null===hasAuthorityPrefixUserPref?-1!==uriParts.source.indexOf("//"):hasAuthorityPrefixUserPref},userInfo=function(val){return"undefined"!=typeof val&&(uriParts.userInfo=val),uriParts.userInfo
},host=function(val){return"undefined"!=typeof val&&(uriParts.host=val),uriParts.host},port=function(val){return"undefined"!=typeof val&&(uriParts.port=val),uriParts.port},path=function(val){return"undefined"!=typeof val&&(uriParts.path=val),uriParts.path},query=function(val){return"undefined"!=typeof val&&(queryObj=new Query(val)),queryObj},anchor=function(val){return"undefined"!=typeof val&&(uriParts.anchor=val),uriParts.anchor},setProtocol=function(val){return protocol(val),this},setHasAuthorityPrefix=function(val){return hasAuthorityPrefix(val),this
},setUserInfo=function(val){return userInfo(val),this},setHost=function(val){return host(val),this},setPort=function(val){return port(val),this},setPath=function(val){return path(val),this},setQuery=function(val){return query(val),this},setAnchor=function(val){return anchor(val),this},getQueryParamValue=function(key){return query().getParamValue(key)},getQueryParamValues=function(key){return query().getParamValues(key)},deleteQueryParam=function(key,val){return 2===arguments.length?query().deleteParam(key,val):query().deleteParam(key),this
},addQueryParam=function(key,val,index){return 3===arguments.length?query().addParam(key,val,index):query().addParam(key,val),this},replaceQueryParam=function(key,newVal,oldVal){return 3===arguments.length?query().replaceParam(key,newVal,oldVal):query().replaceParam(key,newVal),this},toPath=function(val){if(void 0===val)return uriParts.path;if("/"==val.substring(0,1))return uriParts.path=val;var base_path=uriParts.path.split("/"),rel_path=val.split("/");""===base_path.slice(-1)[0]&&base_path.pop();for(var part;part=rel_path.shift();)switch(part){case"..":base_path.length>1&&base_path.pop();
break;case".":break;default:base_path.push(part)}return uriParts.path=base_path.join("/"),this},toString=function(){var s="",is=function(s){return null!==s&&""!==s};return is(protocol())?(s+=protocol(),protocol().indexOf(":")!==protocol().length-1&&(s+=":"),s+="//"):hasAuthorityPrefix()&&is(host())&&(s+="//"),is(userInfo())&&is(host())&&(s+=userInfo(),userInfo().indexOf("@")!==userInfo().length-1&&(s+="@")),is(host())&&(s+=host(),is(port())&&(s+=":"+port())),is(path())?s+=path():is(host())&&(is(query().toString())||is(anchor()))&&(s+="/"),is(query().toString())&&(0!==query().toString().indexOf("?")&&(s+="?"),s+=query().toString()),is(anchor())&&(0!==anchor().indexOf("#")&&(s+="#"),s+=anchor()),s
},clone=function(){return new Uri(toString())};return{protocol:protocol,hasAuthorityPrefix:hasAuthorityPrefix,userInfo:userInfo,host:host,port:port,path:path,query:query,anchor:anchor,setProtocol:setProtocol,setHasAuthorityPrefix:setHasAuthorityPrefix,setUserInfo:setUserInfo,setHost:setHost,setPort:setPort,setPath:setPath,setQuery:setQuery,setAnchor:setAnchor,getQueryParamValue:getQueryParamValue,getQueryParamValues:getQueryParamValues,deleteQueryParam:deleteQueryParam,addQueryParam:addQueryParam,replaceQueryParam:replaceQueryParam,toPath:toPath,toString:toString,clone:clone}
};$.uri=function(s){return new Uri(s)}});