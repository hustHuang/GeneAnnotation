function popuphelp(url)
{
var hWnd = window.open(url,"RePORTERHelp","width=650,height=400,resizable=no,scrollbars=yes");
if (hWnd.focus != null) hWnd.focus();
}
function stripAlphaNumericComma(strInput)
{
var strOutput = new String(strInput);
//strOutput = strOutput.replace(/[^0-9a-zA-Z, (),-]/g, '');
//strOutput=strOutput.replace(/(/g, "");
//strOutput=strOutput.replace(/)/g, "");
strOutput=strOutput.replace(/</g, "");
strOutput=strOutput.replace(/>/g, "");
//strOutput=strOutput.replace(/&amp;/g, "&");
//strOutput=strOutput.replace(/--/g, "");
//strOutput=strOutput.replace(/*/g, "");
strOutput=strOutput.replace(/'/g, "");
//strOutput=strOutput.replace(/"/g, "");
strOutput=strOutput.replace(/%/g, "");
strOutput=strOutput.replace(/#/g, "");
//strOutput=strOutput.replace(/;/g, "");
//strOutput=strOutput.replace(/./g, "");
//strOutput=strOutput.replace(/alert/g, "");
//strOutput=strOutput.replace(/.js/g, "");
return strOutput;
}
//New Code added by SN on 17th Feb
function cc()
{
var url = window.location.pathname
var jsfilename = url.substring(url.lastIndexOf('/')+1);
//alert(jsfilename);
//alert(document.cookie)
/* check for a cookie */
if (jsfilename=="reporter.cfm")
{
if (document.cookie == "")
{
/* if a cookie is not found - alert user -
change cookieexists field value to false */
// alert("COOKIES need to be enabled!");
document.getElementById('cookDiv').style.display = 'block';
} else {
// alert("COOKIES enabled!");
document.getElementById('cookDiv').style.display = 'none';
}
}
}
document.cookie = 'Exist'
//Fn to check blank spaces
function cutSpaces(s)
{
//alert(s)
var s1, x;
x = s1 = "";
l = s.length;
for(i = 0; i < l; i++)
if((x = s.charAt(i)) != " ")
s1+=x;
return s1;
}
function cutats(s)
{
var s1, x;
x = s1 = "";
l = s.length;
for(i = 0; i < l; i++)
if((x = s.charAt(i)) != "@")
s1+=x;
return s1;
}
// New function to check date is valid
function isDate(sDate)
{
var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/
if (re.test(sDate)) {
var dArr = sDate.split("/");
var d = new Date(sDate);
return d.getMonth() + 1 == dArr[0] && d.getDate() == dArr[1] && d.getFullYear() == dArr[2];
}
else {
return false;
}
}
function special_email(var1)
{
if (var1.indexOf('`') >= 0 || var1.indexOf('~') >= 0 || var1.indexOf('!') >= 0 || var1.indexOf('#') >= 0 ||
var1.indexOf('$') >= 0 || var1.indexOf('%') >= 0 || var1.indexOf('^') >= 0 || var1.indexOf('&') >= 0 ||
var1.indexOf('*') >= 0 || var1.indexOf('(') >= 0 || var1.indexOf(')') >= 0 || var1.indexOf('+') >= 0 ||
var1.indexOf('{') >= 0 || var1.indexOf('}') >= 0 || var1.indexOf('|') >= 0 || var1.indexOf('[') >= 0 ||
var1.indexOf(']') >= 0 || var1.indexOf('\\') >= 0 || var1.indexOf(';') >= 0 || var1.indexOf(':') >= 0 ||
var1.indexOf('>') >= 0 || var1.indexOf('<') >= 0 || var1.indexOf(',') >= 0 || var1.indexOf('?') >= 0 ||
var1.indexOf('/') >= 0 || var1.indexOf('\'') >= 0 || var1.indexOf('"') >= 0 || var1.indexOf(' ') >= 0)
{
alert("Enter a valid email address");
return false;
}
return true;
}
function fnValidemail(js_elm)
{
//alert("in")
//alert(js_elm)
email = js_elm.value
// alert(email)
if (cutSpaces(email) == "")
{
alert("Please enter email address");
js_elm.focus();
return false;
}
if (cutSpaces(email) != "")
{
flag=special_email(email);
if(flag==false)
{
js_elm.focus();
return false;
}
var x;
x = email.indexOf("@");
if (email.indexOf("@") <= 0)
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
if (email.substr(email.indexOf("@") - 1, 1) == ".")
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
if (email.indexOf(".") <= 0)
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
if (email.substr(email.length - 2, 1) == "." || email.substr(email.length - 1, 1) == ".")
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
// < 3 chnged to 2
if (email.indexOf(".", email.indexOf("@")) - email.indexOf("@") < 2)
{
alert("Invalid email address.");
//js_elm.value = ""
js_elm.focus();
return false;
}
if (cutats(email).length < email.length - 1)
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
var dom="";
dom=email.substring(email.lastIndexOf("."),email.length)
//alert(dom)
if (email.indexOf(dom)<5)
{
alert("Invalid email address");
js_elm.value = ""
js_elm.focus();
return false;
}
}
} 