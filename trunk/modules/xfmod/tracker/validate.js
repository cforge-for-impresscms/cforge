function strip(filter,str){
        var i,curChar;
        var retStr = '';
        var len = str.length;
        for(i=0; i<len; i++){
                curChar = str.charAt(i);
                if(filter.indexOf(curChar)<0) //not in filter, keep it
                        retStr += curChar;
        }
        return retStr;
}
function reformat(str){
        var arg;
        var pos = 0;
        var retStr = '';
        var len = reformat.arguments.length;
        for(var i=1; i<len; i++){
                arg = reformat.arguments[i];
                if(i%2==1)
                        retStr += arg;
                else{
                        retStr += str.substring(pos, pos + arg);
                        pos += arg;
                }
        }
        return retStr;
}
//End Support Functions
//Validation Rules
function notEmpty(str){
        if(strip(" \n\r\t",str).length ==0)
                return false;
        else
                return true;
}
function validateString(str) {
  var newValue = str;
  var newLength = newValue.length
  var extraChars=". -,"
  var search

  for(var i = 0; i != newLength; i++) {
    aChar = newValue.substring(i,i+1)
    aChar = aChar.toUpperCase()
    search = extraChars.indexOf(aChar)
    if(search == -1 && (aChar < "A" || aChar > "Z") ) {
      return false
    }
  }
return true
}

function validateInteger(str){
        str = strip(' ',str);
        //remove leading zeros, if any
        while(str.length > 1 && str.substring(0,1) == '0'){
                str = str.substring(1,str.length);
        }
        var val = parseInt(str);
        if(isNaN(val))
                return false;
        else
                return true;
}
function validateFloat(str){
        str = strip(' ',str);
        //remove leading zeros, if any
        while(str.length > 1 && str.substring(0,1) == '0'){
                str = str.substring(1,str.length);
        }
        var val = parseFloat(str);
        if(isNaN(val))
                return false;
        else
                return true;
}
function validateUSPhone(str){
        str = strip("*() -./_\n\r\t\\",str);
        if(str.length == 10 || str.length == 7)
                return true;
        else
                return false;
}
function validateSSN(str){
        str = strip(" -.\n\r\t",str);
        if(validateInteger(str) && str.length == 9)
                return true;
        else
                return false;
}
function validateZip(str){
        str = strip("- \n\r\t",str);
        if(validateInteger(str)&&(str.length==9 || str.length==5))
                return true;
        else
                return false;
}
function validateCC(str,type){
        str = strip("-./_\n\r\t\\",str);
        if(type=="1")
                if(str.charAt(0)!="4")
                        return false;
        if(type=="2")
                if(str.charAt(0)!="5")
                        return false;
        if(type=="3")
                if(str.charAt(0)!="6")
                        return false;
        if(type=="4")
                if(str.charAt(0)!="3")
                        return false;
        if(validateInteger(str)&&((str.length==15&&type=="4") || str.length==16))
                return true;
        else
                return false;
}
function validateDate(str){
        var dateVar = new Date(str);
        if(isNaN(dateVar.valueOf()) || (dateVar.valueOf() ==0))
                return false;
        else
                return true;
}
function validateEMail(str){
        str = strip(" \n\r\t",str);
        if(str.indexOf("@")>-1 && str.indexOf(".")>-1)
                return true;
        else
                return false;
}
//End Validation Rules
//Formatting functions
function formatPhone(str){
        str = strip("*() -./_\n\r\t\\",str);
        if(str.length==10)
                return reformat(str,"(",3,") ",3,"-",4);
        if(str.length==7)
                return reformat(str,"",3,"-",4);
}
function formatSSN(str){
        str = strip(" -.\n\r\t",str);
        return reformat(str,"",3,"-",2,"-",4);
}
function formatZip(str){
        str = strip("- \n\r\t",str);
        if(str.length==5)
                return str;
        if(str.length==9)
                return reformat(str,"",5,"-",4);
}
function formatCC(str,type){
        str = strip("-./_\n\r\t\\",str);
        switch(type){
                case "1":
                        return reformat(str,"",4,"-",4,"-",4,"-",4);
                        break;
                case "2":
                        return reformat(str,"",4,"-",4,"-",4,"-",4);
                        break;
                case "3":
                        return reformat(str,"",4,"-",4,"-",4,"-",4);
                        break;
                case "4":
                        return reformat(str,"",4,"-",6,"-",5);
        }
}
function formatDate(str,style){
        var dateVar = new Date(str);
        var year = dateVar.getYear();
        if(year<10)
                year += 2000;
        if(year<100)
                year += 1900;
        switch(style){
                case "MM/DD/YY":
                        return (dateVar.getMonth() + 1) + "/" + dateVar.getDate() + "/" + year;
                        break;
                case "DD/MM/YY":
                        return dateVar.getDate() + "/" + (dateVar.getMonth() + 1) + "/" + year;
                        break;
                case "Month Day, Year":
                        return getMonthName(dateVar) + " " + dateVar.getDate() + ", " + year;
                        break;
                case "Day, Month Day, Year":
                        return getDayName(dateVar) + ", " + getMonthName(dateVar) + " " + dateVar.getDate() + ", " + year;
                        break;
                default:
                        return (dateVar.getMonth() + 1) + "/" + dateVar.getDate() + "/" + year;
                        break;
        }
}
//End Formatting Functions

function isInt(textObj) {

  var newValue = textObj.value
  var newLength = newValue.length
  for(var i = 0; i != newLength; i++) {
    aChar = newValue.substring(i,i+1)
    if(aChar < "0" || aChar > "9") {
      return false
    }
  }
  return true
}

function isString(textObj) {
  var newValue = textObj.value
  var newLength = newValue.length
  var extraChars=". -,"
  var search

  for(var i = 0; i != newLength; i++) {
    aChar = newValue.substring(i,i+1)
    aChar = aChar.toUpperCase()
    search = extraChars.indexOf(aChar)
    if(search == -1 && (aChar < "A" || aChar > "Z") ) {
      return false
    }
  }
return true
}

function moneyFormat(textObj) {
  var newValue = textObj.value
  var decAmount = ""
  var dolAmount = ""
  var decFlag = false
  var aChar = ""
  // ignore all but digits and decimal points.
  for(i=0; i < newValue.length; i++) {
    aChar = newValue.substring(i,i+1)
     if(aChar >= "0" && aChar <= "9") {
       if(decFlag) {
          decAmount = "" + decAmount + aChar
       }
       else {
         dolAmount = "" + dolAmount + aChar
       }
     }
     if(aChar == ".") {
       if(decFlag) {
          dolAmount = ""
          break
       }
       decFlag=true
     }
  }

// Ensure that at least a zero appears for the dollar amount.

if(dolAmount == "") {
  dolAmount = "0"
}
// Strip leading zeros.
if(dolAmount.length > 1) {
  while(dolAmount.length > 1 && dolAmount.substring(0,1) == "0") {
    dolAmount = dolAmount.substring(1,dolAmount.length)
  }
}

// Round the decimal amount.
if(decAmount.length > 2) {
  if(decAmount.substring(2,3) > "4") {
    decAmount = parseInt(decAmount.substring(0,2)) + 1
    if(decAmount < 10) {
      decAmount = "0" + decAmount
    }
    else {
     decAmount = "" + decAmount
   }
 }
else {
  decAmount = decAmount.substring(0,2)
}
if (decAmount == 100) {
  decAmount = "00"
  dolAmount = parseInt(dolAmount) + 1
}
}

// Pad right side of decAmount

if(decAmount.length == 1) {
  decAmount = decAmount + "0"
}
if(decAmount.length == 0) {
  decAmount = decAmount + "00"
}
// Check for negative values and reset textObj
if(newValue.substring(0,1) != '-' ||
        (dolAmount == "0" && decAmount == "00")) {
        textObj.value = dolAmount + "." + decAmount
}
else{
   textObj.value = '-' + dolAmount + "." + decAmount
}
}

function isCreditCard(textObj) {
/*
*  This function validates a credit card entry.
*  If the checksum is ok, the function returns true.
*/

var ccNum
var odd = 1
var even = 2
var calcCard = 0
var calcs = 0
var ccNum2 = ""
var aChar = ''
var cc
var r
ccNum = textObj.value
for(var i = 0; i != ccNum.length; i++) {
  aChar = ccNum.substring(i,i+1)
  if(aChar == '-') {
  continue
  }
  ccNum2 = ccNum2 + aChar
}
cc = parseInt(ccNum2)
if(cc == 0) {
  return false
}
r = ccNum.length / 2
if(ccNum.length - (parseInt(r)*2) == 0) {
  odd = 2
  even = 1
}
for(var x = ccNum.length - 1; x > 0; x--) {
  r = x / 2
  if(r < 1) {
    r++
  }
  if(x - (parseInt(r) * 2) != 0) {
     calcs = (parseInt(ccNum.charAt(x - 1))) * odd
  }
  else {
    calcs = (parseInt(ccNum.charAt(x - 1))) * even
  }
  if(calcs >= 10) {
     calcs = calcs - 10 + 1
  }
  calcCard = calcCard + calcs
}

calcs = 10 - (calcCard % 10)
if(calcs == 10) {
  calcs = 0
}
if(calcs == (parseInt(ccNum.charAt(ccNum.length - 1)))) {
  return true
}
else {
 return false
}
}


<!--

// Globals used by isValid

var DIGITS = "0123456789"

var UPPERS = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"

var LOWERS = "abcdefghijklmnopqrstuvwxyz"



function isValid(obj, picture, detailObj) {

var status = true

var isRepeat = false

var pLen = picture.length

var search = 0

var validData = ""

var picChar = ""

var aChar = ""

var newValue = obj.value

var tLen = newValue.length

var detailError = ""



for(var i = 0, j = 0; (i != newValue.length)

&& (j != picture.length)

&& (status==true)

; i++) {

picChar=picture.substring(j,j+1)

if(picChar == "[") {

validData = ""

j++

for(; j != picture.length; j++) {

if(picture.substring(j,j+1) == "]") {

break

}

validData = validData + picture.substring(j,j+1)

}

}

else if(picChar =="@") {       // Any character

j++

continue

}

else if(picChar == "?") {      // Any letter

validData = UPPERS + LOWERS

}

else if(picChar == "#") {      // Any number

validData = DIGITS

}

else if(picChar =="$") {       // Money characters

validData = DIGITS + "." + "-" + "+"

}

else if(picChar == "*") {

isRepeat = true

j++

i--

continue

}

else {

validData = picChar

}

aChar = newValue.substring(i,i+1)

search = validData.indexOf(aChar)

if(search == -1) {

if(isRepeat) {

isRepeat = false

j++

i--

continue

}

status = false

if(aChar == " ") {

detailError = "A space is not allowed in position #"+(i+1)+". "

}

else {

detailError = "The character, " + aChar +

", is not allowed in position #"+(i+1)+". "

}

}

else {

if(!isRepeat) {

j++

}

}

}

//Check length

if(status == true && (j < picture.length || i < newValue.length)) {

status = false

detailError = "incorrect length"

}

if(detailObj != null) {

detailObj.value = detailError

}

return(status)

}
function ValidateField(theField,fieldType,fieldLength,fieldName,fieldRequired,fieldMask){
    rc=true;

/*
*  check field length.
*/


     if (theField.value.length > fieldLength){
       errMsg="Data is too large for Field:"+fieldName+" max size = "+fieldLength;

       rc=false;
     }

/*
*  check required.
*/
    if (fieldRequired)
       if (!notEmpty(theField.value)){;
         errMsg="Field is required:"+fieldName;
         rc=false;
       }


        if (fieldType=="String"){
	   if (!validateString(theField.value)){
             errMsg="value entered is not String:"+fieldName;
             rc=false;
	   }
	}
	if (fieldType=="int"){
	 if (!validateInteger(theField.value)){
             errMsg="value entered is not Integer:"+fieldName;
             rc=false;
          }
	}
        if (fieldType=="long"){
	  if (!validateInteger(theField.value)){
             errMsg="value entered is not Long:"+fieldName;
              rc=false;
          }
	}
        if (fieldType=="float"){
           if (!validateFloat(theField.value)){
              errMsg="value entered is not Float:"+fieldName;
              rc=false;
           }
	}
	if (fieldType =="date"){
             if (!validateDate(theField.value)){
	      errMsg="value entered is not Date:"+fieldName;

              rc=false;
             }
	     else
              theField.value=formatDate(theField.value,"MM/DD/YY");

	}

	if ((fieldMask!=null) && (fieldMask!="")){
          rc=isValid(theField,fieldMask,Null);
	  if (!rc){
	      errMsg="cannot format data:"+fieldName+" format:"+fieldMask;

              rc=false;

           }
        }
       if(!rc){
            alert(errMsg);
            theField.focus();
        }

      return (rc);

}
