// Global variable to store correct CAPTCHA answer


function showForm(formType) {
  const singleForm = document.getElementById("singleTruckForm");
  const multipleForm = document.getElementById("multipleTruckForm");
  const toggleBtns = document.querySelectorAll(".toggle-btn");
  const validationSummary = document.getElementById("validationSummary");

  // Hide validation summary when switching forms (only if it exists)
  if (validationSummary) {
    validationSummary.style.display = "none";
  }

  // Reset all forms
  if (singleForm) singleForm.classList.remove("active");
  if (multipleForm) multipleForm.classList.remove("active");
  toggleBtns.forEach((btn) => btn.classList.remove("active"));

  if (formType === "single" && singleForm) {
    singleForm.classList.add("active");
    if (toggleBtns[0]) toggleBtns[0].classList.add("active");
    clearValidation(singleForm);
  } else if (formType === "multiple" && multipleForm) {
    multipleForm.classList.add("active");
    if (toggleBtns[1]) toggleBtns[1].classList.add("active");
    clearValidation(multipleForm);
  }

  // Generate new CAPTCHA when switching forms
//   generateCaptcha();
}

// Clear validation states
function clearValidation(form) {
  if (!form) return;
  const inputs = form.querySelectorAll("input, select, textarea");
  inputs.forEach((input) => {
    input.classList.remove("is-invalid", "is-valid");
    const feedback = input.nextElementSibling;
    if (feedback && feedback.classList.contains("invalid-feedback")) {
      feedback.textContent = "";
    }
  });
}

// CAPTCHA function
// function generateCaptcha() {
//   const num1 = Math.floor(Math.random() * 10);
//   const num2 = Math.floor(Math.random() * 10);
//   const operators = ["+", "-"];
//   const operator = operators[Math.floor(Math.random() * operators.length)];

//   let question = `${num1} ${operator} ${num2}`;
//   correctCaptchaAnswer = eval(question);

//   // Update all CAPTCHA questions on the page
//   const captchaQuestions = document.querySelectorAll(
//     '[id*="captcha"], .captcha-question',
//   );
//   captchaQuestions.forEach((element) => {
//     if (
//       element.tagName === "SPAN" ||
//       element.tagName === "P" ||
//       element.tagName === "DIV"
//     ) {
//       element.textContent = `What is ${question}?`;
//     }
//   });

//   // Clear all CAPTCHA inputs and validation
//   const captchaInputs = document.querySelectorAll(
//     'input[name="captcha"], input[name*="captcha"]',
//   );
//   captchaInputs.forEach((input) => {
//     input.value = "";
//     input.classList.remove("is-invalid", "is-valid");
//     const feedback = input.nextElementSibling;
//     if (feedback && feedback.classList.contains("invalid-feedback")) {
//       feedback.textContent = "";
//     }
//   });
// }

// Validation functions
function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePhone(phone) {
  const phoneRegex = /^[\d\s\-\+\(\)]{8,}$/;
  return phoneRegex.test(phone);
}

function validateRegistration(reg) {
  // Australian registration format (flexible)
  const regRegex = /^[A-Z0-9]{3,8}$/i;
  return regRegex.test(reg.replace(/\s/g, ""));
}

function validatePostcode(postcode) {
  // Australian postcode should be 4 digits
  const postcodeRegex = /^\d{4}$/;
  return postcodeRegex.test(postcode);
}

function validateCurrency(value) {
  // Allow currency format with or without $ and commas
  const currencyRegex = /^\$?[\d,]+(\.\d{2})?$/;
  return currencyRegex.test(value.replace(/\s/g, ""));
}

function validateTruckDetails(details) {
  // Should contain year, make, and model
  const parts = details.trim().split(/\s+/);
  return parts.length >= 3 && /\d{4}/.test(details);
}

// function validateCaptcha(answer) {
//   return parseInt(answer) === correctCaptchaAnswer;
// }

// Real-time validation
function setupRealTimeValidation() {
  // Look for all forms, not just those with specific class
  const forms = document.querySelectorAll(
    "form, .quote-form, #singleTruckForm, #multipleTruckForm",
  );

  forms.forEach((form) => {
    if (!form) return;
    const inputs = form.querySelectorAll("input, select, textarea");

    inputs.forEach((input) => {
      // Remove existing listeners to avoid duplicates
      input.removeEventListener("blur", handleFieldValidation);
      input.removeEventListener("input", handleFieldInput);

      // Add new listeners
      input.addEventListener("blur", handleFieldValidation);
      input.addEventListener("input", handleFieldInput);
    });
  });
}

function handleFieldValidation(event) {
  validateField(event.target);
}

function handleFieldInput(event) {
  if (event.target.classList.contains("is-invalid")) {
    validateField(event.target);
  }
}

function validateField(field) {
  if (!field) return true;

  const value = field.value.trim();
  const fieldName = field.name || field.id || "";
  const fieldType = field.type || "";
  const feedback = field.nextElementSibling;
  let isValid = true;
  let message = "";

  // Required field check
  if (field.hasAttribute("required") && !value) {
    isValid = false;
    message = "This field is required.";
  }
  // Email validation
  else if (
    (fieldType === "email" || fieldName.toLowerCase().includes("email")) &&
    value &&
    !validateEmail(value)
  ) {
    isValid = false;
    message = "Please enter a valid email address.";
  }
  // Phone/Contact validation - check multiple possible field identifiers
  else if (
    (fieldType === "tel" ||
      fieldName.toLowerCase().includes("phone") ||
      fieldName.toLowerCase().includes("contact") ||
      fieldName.toLowerCase().includes("mobile")) &&
    value &&
    !validatePhone(value)
  ) {
    isValid = false;
    message = "Please enter a valid phone number.";
  }
  // Name validation (no special characters except spaces, hyphens, apostrophes)
  else if (
    (fieldName.toLowerCase().includes("name") ||
      fieldName.toLowerCase().includes("firstname") ||
      fieldName.toLowerCase().includes("lastname") ||
      fieldName.toLowerCase().includes("full_name") ||
      fieldName.toLowerCase().includes("full-name")) &&
    value
  ) {
    const nameRegex = /^[a-zA-Z\s\-'\.]+$/;
    if (!nameRegex.test(value)) {
      isValid = false;
      message =
        "Please enter a valid name (letters, spaces, hyphens, and apostrophes only).";
    }
  }

  // Update field appearance
  if (isValid) {
    field.classList.remove("is-invalid");
    if (feedback && feedback.classList.contains("invalid-feedback")) {
      feedback.textContent = "";
    }
  } else {
    field.classList.remove("is-valid");
    field.classList.add("is-invalid");
    if (feedback && feedback.classList.contains("invalid-feedback")) {
      feedback.textContent = message;
    }
  }

  return isValid;
}

// Form submission validation
function setupFormSubmission() {
  // Look for all forms, not just those with specific class
  const forms = document.querySelectorAll(
    "form, .quote-form, #singleTruckForm, #multipleTruckForm",
  );

  forms.forEach((form) => {
    if (!form) return;

    // Remove existing listener to avoid duplicates
    form.removeEventListener("submit", handleFormSubmit);
    form.addEventListener("submit", handleFormSubmit);
  });
}

function handleFormSubmit(e) {
  e.preventDefault();

  const form = e.target;
  const isValid = validateForm(form);

  if (isValid) {
    // Hide validation summary
    const validationSummary = document.getElementById("validationSummary");
    if (validationSummary) {
      validationSummary.style.display = "none";
    }

    // Show loading state on button
    const submitBtn =
      form.querySelector(".submit-btn") ||
      form.querySelector('button[type="submit"]') ||
      form.querySelector('input[type="submit"]') ||
      form.querySelector('[type="submit"]');

    if (submitBtn) {
      submitBtn.classList.add("button-loading");
      submitBtn.disabled = true;
    }

    // Get form data for debugging
    const formData = new FormData(form);
    console.log(
      "Form is valid, submitting with data:",
      Object.fromEntries(formData),
    );

    // Create a hidden input to prevent validation on actual submit
    const bypassInput = document.createElement("input");
    bypassInput.type = "hidden";
    bypassInput.name = "validation_bypass";
    bypassInput.value = "true";
    form.appendChild(bypassInput);

    // Remove the event listener temporarily and submit
    form.removeEventListener("submit", handleFormSubmit);

    // Submit the form immediately for valid submissions
    form.submit();

    // NOTE: We do NOT re-enable the button or hide the loader here.
    // The page will reload or navigate away, which is the desired behavior
    // to prevent double submissions.
  } else {
    console.log("Form validation failed");
  }
}

// Helper to create and show loader


function validateForm(form) {
  if (!form) return false;

  const inputs = form.querySelectorAll("input, select, textarea");
  let isFormValid = true;
  const errors = [];

  inputs.forEach((input) => {
    // Only validate required fields and fields with values
    if (input.hasAttribute("required") || input.value.trim()) {
      const isFieldValid = validateField(input);
      if (!isFieldValid && input.hasAttribute("required")) {
        isFormValid = false;
        const label = getFieldLabel(input);
        if (label && !errors.includes(label)) {
          errors.push(label);
        }
      }
    }
  });

  // Validate checkbox groups (e.g. coverage options)
  const checkboxGroups = {};
  form
    .querySelectorAll('input[type="checkbox"][name="coverage_options[]"]')
    .forEach((cb) => {
      const name = cb.name;
      if (!checkboxGroups[name]) {
        checkboxGroups[name] = [];
      }
      checkboxGroups[name].push(cb);
    });

  for (const name in checkboxGroups) {
    const group = checkboxGroups[name];
    const isChecked = group.some((cb) => cb.checked);
    // We assume if the group exists, it is required (based on the asterisk in HTML)
    // Or we could check if the container has a required label.
    // For now, enforcing at least one check for coverage_options
    if (!isChecked) {
      isFormValid = false;
      errors.push("Please select at least one coverage option");
      // Add invalid class to all in group to highlight
      group.forEach((cb) => cb.classList.add("is-invalid"));
    } else {
      group.forEach((cb) => cb.classList.remove("is-invalid"));
    }
  }

  // Special check for CAPTCHA (validate if present)
//   const captchaInputs = form.querySelectorAll(
//     'input[name="captcha"], input[name*="captcha"]',
//   );
//   captchaInputs.forEach((captchaInput) => {
//     if (!captchaInput.value.trim()) {
//       isFormValid = false;
//       errors.push("CAPTCHA verification");
//       captchaInput.classList.add("is-invalid");
//       const feedback = captchaInput.nextElementSibling;
//       if (feedback && feedback.classList.contains("invalid-feedback")) {
//         feedback.textContent = "Please complete the CAPTCHA verification.";
//       }
//     } else if (!validateCaptcha(captchaInput.value.trim())) {
//       isFormValid = false;
//       errors.push("CAPTCHA verification");
//       generateCaptcha();
//     }
//   });

  // Show validation summary if there are errors
  showValidationSummary(isFormValid, errors);

  return isFormValid;
}

function getFieldLabel(input) {
  // Try to find label text in various ways
  let label = "";

  // Look for associated label
  if (input.id) {
    const labelElement = document.querySelector(`label[for="${input.id}"]`);
    if (labelElement) {
      label = labelElement.textContent;
    }
  }

  // Look for label in same container
  if (!label) {
    const container = input.closest(
      ".col-md-4, .col-md-12, .form-group, .field",
    );
    if (container) {
      const labelElement = container.querySelector("label");
      if (labelElement) {
        label = labelElement.textContent;
      }
    }
  }

  // Fallback to input name or placeholder
  if (!label) {
    label = input.name || input.placeholder || "Field";
  }

  return label.replace("*", "").trim();
}

function showValidationSummary(isFormValid, errors) {
  const validationSummary = document.getElementById("validationSummary");
  const validationList = document.getElementById("validationList");

  if (validationSummary && validationList) {
    if (!isFormValid && errors.length > 0) {
      validationList.innerHTML = "";
      errors.forEach((error) => {
        const li = document.createElement("li");
        li.textContent = error;
        validationList.appendChild(li);
      });
      validationSummary.style.display = "block";

      // Scroll to top to show validation summary
      validationSummary.scrollIntoView({ behavior: "smooth", block: "start" });
    } else {
      validationSummary.style.display = "none";
    }
  } 
}

// Initialize validation when page loads
document.addEventListener("DOMContentLoaded", function () {
  console.log("Initializing form validation...");
  setupRealTimeValidation();
  setupFormSubmission();
//   generateCaptcha();

  // Debug: Log found forms
  const forms = document.querySelectorAll(
    "form, .quote-form, #singleTruckForm, #multipleTruckForm",
  );
  console.log("Found forms:", forms.length);
  forms.forEach((form, index) => {
    console.log(`Form ${index + 1}:`, form.id || form.className || "unnamed");
  });
});

// Reinitialize validation after dynamic content changes
function reinitializeValidation() {
  setupRealTimeValidation();
  setupFormSubmission();
//   generateCaptcha();
}
