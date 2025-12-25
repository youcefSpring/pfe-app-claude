// Multi-select toggle functions
function toggleSpecialitySelection() {
    const select = document.getElementById('specialities');
    const button = event.target.closest('button');
    
    if (!select) return;
    
    const allOptions = Array.from(select.options);
    const allSelected = allOptions.every(option => option.selected);
    
    if (allSelected) {
        // Deselect all
        allOptions.forEach(option => option.selected = false);
        button.innerHTML = '<i class="bi bi-check-all me-1"></i>@lang('app.select_all')';
    } else {
        // Select all
        allOptions.forEach(option => option.selected = true);
        button.innerHTML = '<i class="bi bi-x-square me-1"></i>@lang('app.deselect_all')';
    }
    
    // Trigger change event for validation
    select.dispatchEvent(new Event('change', { bubbles: true }));
}

function toggleSpecialitySelectionStudent() {
    const select = document.getElementById('specialities_student');
    const button = event.target.closest('button');
    
    if (!select) return;
    
    const allOptions = Array.from(select.options);
    const allSelected = allOptions.every(option => option.selected);
    
    if (allSelected) {
        // Deselect all
        allOptions.forEach(option => option.selected = false);
        button.innerHTML = '<i class="bi bi-check-all me-1"></i>@lang('app.select_all')';
    } else {
        // Select all
        allOptions.forEach(option => option.selected = true);
        button.innerHTML = '<i class="bi bi-x-square me-1"></i>@lang('app.deselect_all')';
    }
    
    // Trigger change event for validation
    select.dispatchEvent(new Event('change', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize multi-select buttons state
    const selectIds = ['specialities', 'specialities_student'];
    selectIds.forEach(function(selectId) {
        const select = document.getElementById(selectId);
        const button = select ? select.parentElement.querySelector('button') : null;
        
        if (select && button) {
            const updateButtonText = function() {
                const allOptions = Array.from(select.options);
                const allSelected = allOptions.every(function(option) {
                    return option.selected;
                });
                
                if (allSelected && allOptions.length > 0) {
                    button.innerHTML = '<i class="bi bi-x-square me-1"></i>@lang("app.deselect_all")';
                } else {
                    button.innerHTML = '<i class="bi bi-check-all me-1"></i>@lang("app.select_all")';
                }
            };
            
            select.addEventListener('change', updateButtonText);
            updateButtonText(); // Initialize button text
        }
    });
});