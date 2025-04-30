/**
 * Animations for Laundry Management System
 */

// Execute when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Apply animations to elements with animation classes
    initializeAnimations();
    
    // Add animation listeners for elements that need specific triggers
    addAnimationListeners();
});

/**
 * Initialize animations for elements with animation classes
 */
function initializeAnimations() {
    // Fade in animations
    const fadeElements = document.querySelectorAll('.animate-fade-in');
    fadeElements.forEach(element => {
        animateElement(element, 'fadeIn');
    });
    
    // Slide in from left animations
    const slideLeftElements = document.querySelectorAll('.animate-slide-left');
    slideLeftElements.forEach(element => {
        animateElement(element, 'slideInLeft');
    });
    
    // Slide in from right animations
    const slideRightElements = document.querySelectorAll('.animate-slide-right');
    slideRightElements.forEach(element => {
        animateElement(element, 'slideInRight');
    });
    
    // Bounce animations
    const bounceElements = document.querySelectorAll('.animate-bounce');
    bounceElements.forEach(element => {
        animateElement(element, 'bounce');
    });
    
    // Pulse animations
    const pulseElements = document.querySelectorAll('.animate-pulse');
    pulseElements.forEach(element => {
        animateElement(element, 'pulse');
    });
}

/**
 * Add event listeners for animations that require specific triggers
 */
function addAnimationListeners() {
    // Add hover animations
    const hoverElements = document.querySelectorAll('.animate-on-hover');
    hoverElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const animationType = this.dataset.hoverAnimation || 'pulse';
            animateElement(this, animationType);
        });
    });
    
    // Add click animations
    const clickElements = document.querySelectorAll('.animate-on-click');
    clickElements.forEach(element => {
        element.addEventListener('click', function(e) {
            const animationType = this.dataset.clickAnimation || 'bounce';
            animateElement(this, animationType);
        });
    });
    
    // Action buttons in tables
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.classList.add('animate__animated', 'animate__pulse');
        });
        
        button.addEventListener('mouseleave', function() {
            this.classList.remove('animate__animated', 'animate__pulse');
        });
        
        button.addEventListener('click', function() {
            if (this.classList.contains('delete-btn')) {
                shakeElement(this);
            }
        });
    });
}

/**
 * Animate an element with the specified animation
 * 
 * @param {HTMLElement} element - The element to animate
 * @param {string} animationName - Name of the animation to apply
 */
function animateElement(element, animationName = 'fadeIn') {
    // Define animation classes based on animation name
    let animationClass = '';
    let animationDuration = element.dataset.animationDuration || '0.5s';
    
    switch(animationName) {
        case 'fadeIn':
            element.style.opacity = '0';
            setTimeout(() => {
                element.style.transition = `opacity ${animationDuration} ease-in-out`;
                element.style.opacity = '1';
            }, 50);
            break;
            
        case 'slideInLeft':
            element.style.transform = 'translateX(-50px)';
            element.style.opacity = '0';
            setTimeout(() => {
                element.style.transition = `transform ${animationDuration} ease-out, opacity ${animationDuration} ease-in-out`;
                element.style.transform = 'translateX(0)';
                element.style.opacity = '1';
            }, 50);
            break;
            
        case 'slideInRight':
            element.style.transform = 'translateX(50px)';
            element.style.opacity = '0';
            setTimeout(() => {
                element.style.transition = `transform ${animationDuration} ease-out, opacity ${animationDuration} ease-in-out`;
                element.style.transform = 'translateX(0)';
                element.style.opacity = '1';
            }, 50);
            break;
            
        case 'bounce':
            element.style.transition = `transform ${animationDuration} cubic-bezier(0.175, 0.885, 0.32, 1.275)`;
            element.style.transform = 'scale(0.9)';
            setTimeout(() => {
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 100);
            }, 100);
            break;
            
        case 'pulse':
            element.style.transition = `transform ${animationDuration} ease-in-out`;
            element.style.transform = 'scale(1.05)';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
            break;
            
        default:
            // Default to fade in
            element.style.opacity = '0';
            setTimeout(() => {
                element.style.transition = `opacity ${animationDuration} ease-in-out`;
                element.style.opacity = '1';
            }, 50);
    }
}

/**
 * Shake an element (for error or warning indicators)
 * 
 * @param {HTMLElement} element - The element to shake
 */
function shakeElement(element) {
    element.style.transition = 'transform 0.1s ease-in-out';
    element.style.transform = 'translateX(-5px)';
    
    setTimeout(() => {
        element.style.transform = 'translateX(5px)';
        setTimeout(() => {
            element.style.transform = 'translateX(-5px)';
            setTimeout(() => {
                element.style.transform = 'translateX(5px)';
                setTimeout(() => {
                    element.style.transform = 'translateX(0)';
                }, 100);
            }, 100);
        }, 100);
    }, 100);
}

/**
 * Add animation to table rows (for use in customer, order lists etc.)
 */
function animateTableRows() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        setTimeout(() => {
            row.style.transition = `opacity 0.3s ease-in-out`;
            row.style.opacity = '1';
        }, 50 * (index + 1));
    });
}

/**
 * Animate form validation feedback
 * 
 * @param {HTMLElement} formField - The form field to animate
 * @param {boolean} isValid - Whether the field is valid
 */
function animateFormValidation(formField, isValid) {
    if (isValid) {
        formField.classList.add('is-valid');
        formField.classList.remove('is-invalid');
    } else {
        formField.classList.add('is-invalid');
        formField.classList.remove('is-valid');
        shakeElement(formField);
    }
}
