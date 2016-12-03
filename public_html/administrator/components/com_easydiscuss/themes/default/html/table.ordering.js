ed.require(['edq', 'easydiscuss'], function($) {

    var orderingButton = $('[data-ed-ordering]');
    var form = $('[data-ed-form]');
    var formTask = $('[data-ed-form-task]');

    orderingButton.on('click', function() {

        var row = $(this).parents('tr');
        var task = $(this).data('task');
        var checkbox = row.find('input[name=cid\\[\\]]');
        
        // Check the checkbox
        checkbox.attr('checked', 'checked');

        // Set the task
        formTask.val(task);

        // Submit the form
        form.submit();
    });

});