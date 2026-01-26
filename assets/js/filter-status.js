// Tambahkan script ini sebelum closing tag </body> di file PHP Anda

document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('#filter-buttons button');
    const cardItems = document.querySelectorAll('.card-item');
    const emptyFilterState = document.getElementById('empty-filter-state');
    const noDataMessage = document.getElementById('no-data-message');

    // Debug: Log semua status yang ada di database
    console.log('=== DEBUG STATUS ===');
    cardItems.forEach((card, index) => {
        const status = card.getAttribute('data-status');
        console.log(`Card ${index + 1}: Status = "${status}" (length: ${status ? status.length : 0})`);
    });

    // Function to normalize status (remove extra spaces, standardize case)
    function normalizeStatus(status) {
        if (!status) return '';
        return status.trim(); // Hanya trim spasi, keep original case
    }

    // Function to filter cards
    function filterCards(status) {
        console.log(`=== FILTERING BY: "${status}" ===`);
        
        let visibleCount = 0;
        
        cardItems.forEach((card, index) => {
            const cardStatus = normalizeStatus(card.getAttribute('data-status'));
            const filterStatus = normalizeStatus(status);
            
            console.log(`Card ${index + 1}: "${cardStatus}" vs Filter: "${filterStatus}"`);
            
            let shouldShow = false;
            
            if (filterStatus === 'all') {
                shouldShow = true;
            } else {
                // Exact match with normalized strings
                shouldShow = cardStatus === filterStatus;
            }
            
            if (shouldShow) {
                card.classList.remove('hidden');
                card.classList.add('fade-in');
                visibleCount++;
                
                // Stagger animation
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
                
                console.log(`  → SHOWN`);
            } else {
                card.classList.add('hidden');
                card.classList.remove('fade-in');
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                
                console.log(`  → HIDDEN`);
            }
        });

        console.log(`Total visible: ${visibleCount}`);

        // Show/hide empty state
        if (visibleCount === 0 && cardItems.length > 0) {
            if (emptyFilterState) emptyFilterState.style.display = 'block';
            if (noDataMessage) noDataMessage.style.display = 'none';
        } else {
            if (emptyFilterState) emptyFilterState.style.display = 'none';
            if (noDataMessage && visibleCount === 0 && cardItems.length === 0) {
                noDataMessage.style.display = 'block';
            }
        }
    }

    // Function to count items by status
    function updateButtonCounts() {
        console.log('=== UPDATING COUNTS ===');
        
        // Get all unique statuses from cards
        const uniqueStatuses = new Set();
        cardItems.forEach(card => {
            const status = normalizeStatus(card.getAttribute('data-status'));
            if (status) uniqueStatuses.add(status);
        });
        
        console.log('Unique statuses found:', Array.from(uniqueStatuses));

        const statusCounts = {
            'all': cardItems.length,
            'Menunggu': 0,
            'Disetujui': 0,
            'Ditolak': 0,
            'Selesai': 0
        };

        // Count items for each status
        cardItems.forEach(card => {
            const status = normalizeStatus(card.getAttribute('data-status'));
            if (statusCounts.hasOwnProperty(status)) {
                statusCounts[status]++;
            }
        });

        console.log('Status counts:', statusCounts);

        // Update count badges if they exist
        const countElements = {
            'all': document.getElementById('count-all'),
            'Menunggu': document.getElementById('count-menunggu'),
            'Disetujui': document.getElementById('count-disetujui'),
            'Ditolak': document.getElementById('count-ditolak'),
            'Selesai': document.getElementById('count-selesai')
        };

        Object.keys(statusCounts).forEach(status => {
            const countElement = countElements[status];
            if (countElement) {
                countElement.textContent = statusCounts[status];
                
                // Show/hide badges based on count
                if (statusCounts[status] === 0) {
                    countElement.style.display = 'none';
                } else {
                    countElement.style.display = 'inline';
                }
            }
        });

        // Also update button text if no badges
        filterButtons.forEach(button => {
            const buttonStatus = button.getAttribute('data-status');
            const count = buttonStatus === 'all' ? statusCounts.all : statusCounts[buttonStatus] || 0;
            
            // Only update if there's no badge element
            if (!countElements[buttonStatus]) {
                const originalText = button.textContent.split(' (')[0];
                button.textContent = `${originalText} (${count})`;
            }
        });
    }

    // Add click event listeners to filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the status to filter
            const status = this.getAttribute('data-status');
            
            console.log(`Button clicked: "${status}"`);
            
            // Filter cards
            filterCards(status);
        });
    });

    // Add smooth transition for cards
    cardItems.forEach(card => {
        card.style.transition = 'opacity 0.3s ease-in-out, transform 0.3s ease-in-out';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    });

    // Initialize counts and setup on page load
    updateButtonCounts();

    // Add keyboard navigation
    filterButtons.forEach((button, index) => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && index > 0) {
                filterButtons[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < filterButtons.length - 1) {
                filterButtons[index + 1].focus();
            }
        });
    });

    console.log(`Filter initialized with ${cardItems.length} total items`);
});