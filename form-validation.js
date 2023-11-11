/********w************
    
	Project 4 
	Name: Gabriel Datuin
	Date: 
	Description: Validates form elements for a contact form.

*********************/

function load() {

	hideErrors()

    document.getElementById("form").addEventListener("submit", validate);

}

function validate(e) {
	
	hideErrors();
	if (formHasErrors()) {
		e.preventDefault();
		return false;
	}
    
	return true;
}

function formHasErrors() {
    
    let errorFlag = false
	const contactTextFields = ['fullname', 'phone', 'email']


	for(textfields of contactTextFields) {
		if(!checkForInput(document.getElementById(textfields))) {
			document.getElementById(textfields + "_error").style.display = "block"

			if (!errorFlag) {
				document.getElementById(textfields).focus()
                document.getElementById(textfields).select()

			}

			errorFlag = true
		}

    }
    
     const phoneRegex = /(\+\d{1,3}\s?)?((\(\d{3}\)\s?)|(\d{3})(\s|-?))(\d{3}(\s|-?))(\d{4})(\s?(([E|e]xt[:|.|]?)|x|X)(\s?\d+))?/g;
     const phoneValue = document.getElementById("phone").value

     if (!phoneRegex.test(phoneValue)) {
		document.getElementById("phoneformat_error").style.display = "block"
		
        if (!errorFlag) {
        document.getElementById("phone").focus()
		document.getElementById("phone").select()

        }

		errorFlag = true

	 }

     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	 const emailValue = document.getElementById("email").value

	 if (!emailRegex.test(emailValue)) {
		document.getElementById("emailformat_error").style.display = "block"    

        if (!errorFlag) {
		document.getElementById("email").focus()
		document.getElementById("email").select()
		
        }


		errorFlag = true

	 }

     



     return errorFlag

     
}

function checkForInput(textfield) {
	
	if (textfield.value == null || textfield.value.trim() == "") {
	
		return false;
	}

	return true;
}

function hideErrors() {
	
	let error = document.getElementsByClassName("error");
	for (let i = 0; i < error.length; i++) {
		error[i].style.display = "none";
	}
}



document.addEventListener("DOMContentLoaded", load);
