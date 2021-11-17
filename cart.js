var sum=0;
function chkcontrol(j) {
    var check= document.getElementById(j);
    if(check.checked){
        sum = sum + parseInt(j);
    }
    else{
        sum = sum - parseInt(j);
    }
    document.getElementById("price").innerHTML= sum;
}
