// NeonTask Main JS

document.addEventListener('DOMContentLoaded', function() {
    // Filter buttons
    document.querySelectorAll('.neon-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.neon-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.neon-task-item').forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    // Add/Edit/Delete/Complete task handlers would go here (AJAX to PHP)
    document.querySelectorAll('.neon-task-item form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = this.querySelector('button[name="action"]').value;
            const taskItem = this.closest('.neon-task-item');
            const taskTitle = taskItem.querySelector('.task-title');
            if (action === 'complete') {
                // Add strikethrough immediately
                taskTitle.classList.add('task-completed');
            } else if (action === 'undo') {
                // Remove strikethrough
                taskTitle.classList.remove('task-completed');
            }
            // Allow form to submit normally for backend update
        });
    });
}); 