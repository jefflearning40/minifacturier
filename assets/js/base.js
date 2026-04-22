    document.addEventListener('DOMContentLoaded', function () {
                    const flashModalElement = document.getElementById('flashModal');

                    if (flashModalElement) {
                        const flashModal = new bootstrap.Modal(flashModalElement);
                        flashModal.show();
                    }
                });