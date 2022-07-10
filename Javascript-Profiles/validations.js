function ValidateEdit()
{
    console.log('Validating...');

    try
    {
        var fn = document.getElementById('fn').value;
        console.log('Validating...');
        var ln = document.getElementById('ln').value;
        console.log('Validating...');
        var ema = document.getElementById('ema').value;
        var hd = document.getElementById('hd').value;
        var sum = document.getElementById('sum').value;
        var n = ema.indexOf("@");

        if(fn==null || fn=="" || ln==null || ln=="" || ema==null || ema=="" || hd==null || hd=="" || sum==null || sum=="")
        {
        	alert("All fields are required");
        	return false;
        }
        if (n==-1)
        {
        alert("Email address must contain @");
        return false;
        }

        return true;
    }

    catch(e)
    {
        return false;
    }

    return false;
}


function validateLogIn()
{
    console.log('Validating...');
    try
    {
        pw = document.getElementById('id_1723').value;
        nm = document.getElementById('email').value;
        var n = nm.indexOf("@");
        console.log("Validating pw and email"+pw);
        if(nm==null || n==-1)
        {
        	alert("Invalid email address");
        	return false;
        }
        if (pw == null || pw == "")
        {
        alert("Both fields must be filled out");
        return false;
        }

        return true;
    }
    catch(e)
    {
        return false;
    }

    return false;
}