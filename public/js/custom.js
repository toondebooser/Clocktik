      document.addEventListener('DOMContentLoaded', function() {
            function openConfirmationModal(message, actionUrl) {
                document.getElementById('modalText').innerText = message;
                document.getElementById('confirmButton').dataset.url = actionUrl;
                document.getElementById('confirmationModal').style.display = "block";
            }
    
            function closeModal() {
                document.getElementById('confirmationModal').style.display = "none";
            }
    
            document.getElementById('confirmButton').onclick = function() {
                const actionUrl = this.dataset.url;
    
                fetch('/confirm-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                        },
                        body: JSON.stringify({
                            action: actionUrl
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = actionUrl;
                        } else {
                            alert('Failed to confirm action.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
    
            window.onclick = function(event) {
                const modal = document.getElementById('confirmationModal');
                if (event.target == modal) {
                    closeModal();
                }
            };
    
            window.openConfirmationModal = openConfirmationModal;
            window.closeModal = closeModal;
        });