document.addEventListener('click', function (event) {
    const deleteTrigger = event.target.closest('[data-confirm-delete]');

    if (deleteTrigger) {
        event.preventDefault();

        if (confirm('Are you sure? It will delete the data permanently!')) {
            document.getElementById(deleteTrigger.dataset.confirmDelete).submit();
        }

        return;
    }

    const submitTrigger = event.target.closest('[data-submit-form]');

    if (submitTrigger) {
        event.preventDefault();
        document.getElementById(submitTrigger.dataset.submitForm).submit();
    }
});
