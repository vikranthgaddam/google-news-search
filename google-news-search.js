jQuery(document).ready(function($) {
    // Function to perform AJAX search
    function performSearch(searchTerm, page = 1) {
        // Check if the user is online
        if (!navigator.onLine) {
            $('#search-results').html('<p>You are currently offline. Please check your internet connection.</p>');
            $('#pagination').html('');
            return;
        }

        // Show loading message
        $('#search-results').html('<p>Loading...</p>');

        $.ajax({
            url: googleNewsSearch.ajaxurl,
            type: 'POST',
            data: {
                action: 'google_news_search',
                search_term: searchTerm,
                nonce: googleNewsSearch.nonce,
                page: page
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#search-results').html('<p>' + response.error + '</p>');
                    $('#pagination').html('');
                } else {
                    $('#search-results').html(response.results);
                    $('#pagination').html(response.pagination);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " - " + error);
                $('#search-results').html('<p>Error fetching results. Please try again.</p>');
                $('#pagination').html('');
            }
        });
    }

    // Handle form submission
    $('#google-news-search-form').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#search-term').val();
        performSearch(searchTerm);
    });

    // Handle pagination click
    $(document).on('click', '.page-number', function(e) {
        e.preventDefault();
        var searchTerm = $('#search-term').val();
        var page = $(this).data('page');
        performSearch(searchTerm, page);
    });

    // Function to update online status message
    function updateOnlineStatus() {
        if (!navigator.onLine) {
            $('#search-results').html('<p>You are currently offline. Please check your internet connection.</p>');
            $('#pagination').html('');
        }
    }

    // Add event listeners for online and offline events
    window.addEventListener('offline', updateOnlineStatus);
    window.addEventListener('online', function() {
        $('#search-results').html(''); // Clear the message when back online
        $('#pagination').html('');     // Clear the pagination message
    });
});
