<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="header-left">
        <h1 class="page-title">Add New Property</h1>
        <p class="page-subtitle">List a new property for rent</p>
    </div>
    <div class="header-actions">
        <a href="<?php echo URLROOT; ?>/landlord/properties" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Properties
        </a>
    </div>
</div>

<!-- Add Property Form -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Property Information</h2>
    </div>
    <div class="card-body">
        <form id="addPropertyForm" action="<?php echo URLROOT; ?>/landlord/add_property" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Property Address *</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Property Type *</label>
                        <select class="form-control" id="property_type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="condo">Condo</option>
                            <option value="townhouse">Townhouse</option>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Bedrooms *</label>
                            <select class="form-control" id="bedrooms" name="bedrooms" required>
                                <option value="">Select</option>
                                <option value="0">Studio</option>
                                <option value="1">1 Bedroom</option>
                                <option value="2">2 Bedrooms</option>
                                <option value="3">3 Bedrooms</option>
                                <option value="4">4+ Bedrooms</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bathrooms *</label>
                            <select class="form-control" id="bathrooms" name="bathrooms" required>
                                <option value="">Select</option>
                                <option value="1">1 Bathroom</option>
                                <option value="2">2 Bathrooms</option>
                                <option value="3">3+ Bathrooms</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Square Footage</label>
                        <input type="number" class="form-control" id="sqft" name="sqft" placeholder="e.g., 1200">
                    </div>

                    <!-- ✨ NEW: Rent Optimizer Section -->
                    <div class="form-group">
                        <div class="rent-optimizer-trigger">
                            <div class="optimizer-content">
                                <div class="optimizer-icon">
                                    <i class="fas fa-brain"></i>
                                </div>
                                <div class="optimizer-text">
                                    <h4>Smart Rent Suggestion</h4>
                                    <p>Let AI analyze 100+ Colombo properties to suggest optimal rent</p>
                                </div>
                            </div>
                            <button type="button"
                                class="btn-optimizer"
                                id="suggestRentBtn"
                                onclick="getSuggestedRent()">
                                <i class="fas fa-chart-line"></i> Get Suggestion
                            </button>
                        </div>
                    </div>

                    <!--  NEW: Rent Suggestion Result Box -->
                    <div id="rentSuggestion" class="rent-suggestion-box" style="display: none;">
                        <div class="suggestion-header">
                            <h4><i class="fas fa-robot"></i> AI Rent Analysis</h4>
                            <button type="button" class="close-btn" onclick="closeSuggestion()">×</button>
                        </div>

                        <div class="suggestion-content" id="suggestionContent">
                            <!-- Market Average -->
                            <div class="stat-item">
                                <span class="stat-label">Market Average:</span>
                                <span class="stat-value" id="marketAverage">-</span>
                            </div>

                            <!-- Suggested Rent (Main) -->
                            <div class="stat-item primary">
                                <span class="stat-label">Recommended Rent:</span>
                                <span class="stat-value" id="suggestedRent">-</span>
                            </div>

                            <!-- Rent Range -->
                            <div class="stat-item">
                                <span class="stat-label">Competitive Range:</span>
                                <span class="stat-value" id="rentRange">-</span>
                            </div>

                            <!-- Confidence Score -->
                            <div class="stat-item">
                                <span class="stat-label">Confidence:</span>
                                <span class="stat-value">
                                    <span id="confidenceScore">-</span>
                                    <div class="confidence-bar">
                                        <div id="confidenceBarFill" class="confidence-fill"></div>
                                    </div>
                                </span>
                            </div>

                            <!-- Similar Properties Count -->
                            <div class="stat-item">
                                <span class="stat-label">Analysis Based On:</span>
                                <span class="stat-value" id="similarCount">-</span>
                            </div>

                            <!-- Breakdown -->
                            <div class="breakdown-section" id="breakdown" style="display: none;">
                                <h5>Price Adjustments:</h5>
                                <ul id="breakdownList"></ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="suggestion-actions">
                                <button type="button"
                                    class="btn btn-primary btn-accept"
                                    onclick="acceptSuggestion()">
                                    <i class="fas fa-check-circle"></i> Use This Rent
                                </button>
                                <button type="button"
                                    class="btn btn-outline"
                                    onclick="closeSuggestion()">
                                    <i class="fas fa-times"></i> Dismiss
                                </button>
                            </div>
                        </div>

                        <div class="suggestion-loading" id="suggestionLoading" style="display: none;">
                            <div class="spinner"></div>
                            <p>🔍 Analyzing 100+ Colombo properties...</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monthly Rent (Rs) *</label>
                        <input type="number"
                            class="form-control"
                            id="rent"
                            name="rent"
                            required
                            placeholder="e.g., 25000"
                            min="1000"
                            step="100">
                        <small style="color: var(--text-secondary); font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> Use AI suggestion above or enter custom amount
                        </small>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Security Deposit (Rs)</label>
                        <input type="number"
                            class="form-control"
                            id="deposit"
                            name="deposit"
                            placeholder="e.g., 25000"
                            step="100">
                        <small style="color: var(--text-secondary); font-size: 0.875rem;">Typically 1-2 months rent</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Available Date</label>
                        <input type="date" class="form-control" name="available_date">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Parking Spaces</label>
                        <select class="form-control" id="parking" name="parking">
                            <option value="0">No Parking</option>
                            <option value="1">1 Space</option>
                            <option value="2">2 Spaces</option>
                            <option value="3">3+ Spaces</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pet Policy</label>
                        <select class="form-control" id="pet_policy" name="pets">
                            <option value="no">No Pets</option>
                            <option value="cats">Cats Only</option>
                            <option value="dogs">Dogs Only</option>
                            <option value="both">Cats & Dogs</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Laundry Facilities</label>
                        <select class="form-control" id="laundry" name="laundry">
                            <option value="none">No Laundry</option>
                            <option value="shared">Shared Laundry</option>
                            <option value="hookups">Washer/Dryer Hookups</option>
                            <option value="in_unit">In-Unit Washer/Dryer</option>
                            <option value="included">Washer/Dryer Included</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Property Description</label>
                <textarea class="form-control" name="description" rows="4" placeholder="Describe the property, amenities, neighborhood, etc."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Property Photos</label>
                <input type="file" class="form-control" name="photos" multiple accept="image/*">
                <small style="color: var(--text-secondary); font-size: 0.875rem;">Upload multiple photos (JPG, PNG, max 5MB each)</small>
            </div>

            <div class="form-group">
                <label class="form-label">Additional Documents</label>
                <input type="file" class="form-control" name="documents" multiple>
                <small style="color: var(--text-secondary); font-size: 0.875rem;">Upload lease templates, property documents, etc.</small>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Property
                </button>
            </div>
        </form>
    </div>
</div>

<!--  NEW: Styles for Rent Optimizer -->
<style>
    /* Rent Optimizer Trigger - Updated Blue Gradient */
    .rent-optimizer-trigger {
        background: linear-gradient(135deg, #45a9eb 0%, #1e88e5 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 20px rgba(69, 169, 235, 0.35);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .rent-optimizer-trigger:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(69, 169, 235, 0.5);
    }

    .optimizer-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .optimizer-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }

    .optimizer-text {
        flex: 1;
        color: white;
    }

    .optimizer-text h4 {
        margin: 0 0 0.25rem 0;
        font-size: 1.125rem;
        font-weight: 700;
    }

    .optimizer-text p {
        margin: 0;
        font-size: 0.875rem;
        opacity: 0.95;
    }

    .btn-optimizer {
        width: 100%;
        background: white !important;
        color: #45a9eb !important;
        border: none !important;
        padding: 0.875rem 1.5rem !important;
        border-radius: 0.75rem !important;
        font-weight: 700 !important;
        font-size: 1rem !important;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .btn-optimizer:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        background: #f8f9fa !important;
    }

    .btn-optimizer:active {
        transform: translateY(0);
    }

    /* Rent Suggestion Box */
    .rent-suggestion-box {
        background: white;
        border: 3px solid #10b981;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
        animation: slideDown 0.4s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .suggestion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .suggestion-header h4 {
        margin: 0;
        color: #059669;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.75rem;
        color: #6b7280;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .close-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .suggestion-content {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        border-left: 4px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: #f3f4f6;
    }

    .stat-item.primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        padding: 1.25rem 1rem;
        border-left: 4px solid #047857;
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.02);
        }
    }

    .stat-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.938rem;
    }

    .stat-item.primary .stat-label {
        color: white;
        font-size: 1rem;
    }

    .stat-value {
        font-weight: 700;
        color: #1f2937;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-item.primary .stat-value {
        color: white;
        font-size: 1.625rem;
    }

    .confidence-bar {
        width: 120px;
        height: 12px;
        background: #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        display: inline-block;
        margin-left: 0.5rem;
    }

    .confidence-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 6px;
    }

    .breakdown-section {
        margin-top: 1rem;
        padding: 1rem;
        background: #f0fdf4;
        border-radius: 0.5rem;
        border: 2px dashed #86efac;
    }

    .breakdown-section h5 {
        margin: 0 0 0.75rem 0;
        font-size: 1rem;
        color: #065f46;
        font-weight: 700;
    }

    .breakdown-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .breakdown-section li {
        padding: 0.625rem 0;
        border-bottom: 1px solid #d1fae5;
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
    }

    .breakdown-section li:last-child {
        border-bottom: none;
    }

    .suggestion-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 2px solid #e5e7eb;
    }

    .suggestion-actions .btn {
        flex: 1;
    }

    .btn-accept {
        animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {

        0%,
        100% {
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
        }
    }

    .suggestion-loading {
        text-align: center;
        padding: 3rem 2rem;
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #e5e7eb;
        border-top-color: #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .suggestion-loading p {
        color: #6b7280;
        margin: 0;
        font-weight: 600;
        font-size: 1.125rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .optimizer-content {
            flex-direction: column;
            text-align: center;
        }

        .suggestion-actions {
            flex-direction: column;
        }

        .stat-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .confidence-bar {
            width: 100%;
        }
    }
</style>

<!--  NEW: JavaScript for Rent Optimizer -->
<script>
    const URLROOT = '<?php echo URLROOT; ?>';
    let suggestedRentValue = 0;

    // Get suggested rent based on form data
    function getSuggestedRent() {
        // Get form values
        const address = document.getElementById('address')?.value.trim() || '';
        const propertyType = document.getElementById('property_type')?.value;
        const bedrooms = document.getElementById('bedrooms')?.value;
        const bathrooms = document.getElementById('bathrooms')?.value;
        const sqft = document.getElementById('sqft')?.value;
        const parking = document.getElementById('parking')?.value;
        const petPolicy = document.getElementById('pet_policy')?.value;
        const laundry = document.getElementById('laundry')?.value;

        // Validation
        if (!propertyType || !bedrooms || !bathrooms) {
            alert('⚠️ Please fill in Property Type, Bedrooms, and Bathrooms first');
            return;
        }

        // Show suggestion box and loading
        const suggestionBox = document.getElementById('rentSuggestion');
        const suggestionContent = document.getElementById('suggestionContent');
        const suggestionLoading = document.getElementById('suggestionLoading');

        suggestionBox.style.display = 'block';
        suggestionContent.style.display = 'none';
        suggestionLoading.style.display = 'block';

        // Scroll to suggestion box
        suggestionBox.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Prepare data
        const formData = new URLSearchParams({
            address: address,
            property_type: propertyType,
            bedrooms: bedrooms,
            bathrooms: bathrooms,
            sqft: sqft,
            parking: parking,
            pet_policy: petPolicy,
            laundry: laundry
        });

        // Call API
        fetch(URLROOT + '/landlord/suggestRent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                suggestionLoading.style.display = 'none';
                suggestionContent.style.display = 'flex';

                if (data.success) {
                    displaySuggestion(data);
                } else {
                    alert('❌ ' + (data.message || 'Could not generate suggestion. Please enter rent manually.'));
                    closeSuggestion();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred. Please try again or enter rent manually.');
                closeSuggestion();
            });
    }

    // Display suggestion
    function displaySuggestion(data) {
        suggestedRentValue = data.suggested_rent;

        // Market average
        document.getElementById('marketAverage').textContent = 'Rs ' + formatNumber(data.market_average);

        // Suggested rent
        document.getElementById('suggestedRent').textContent = 'Rs ' + formatNumber(data.suggested_rent);

        // Rent range
        document.getElementById('rentRange').textContent =
            'Rs ' + formatNumber(data.rent_range.min) + ' - Rs ' + formatNumber(data.rent_range.max);

        // Confidence
        document.getElementById('confidenceScore').textContent = data.confidence + '%';
        document.getElementById('confidenceBarFill').style.width = data.confidence + '%';

        // Similar properties count
        const sources = data.data_sources || {};
        const totalSimilar = (sources.real || 0) + (sources.market || 0);
        document.getElementById('similarCount').textContent =
            totalSimilar + ' similar Colombo properties';

        // Breakdown
        if (data.breakdown && data.breakdown.length > 0) {
            const breakdownList = document.getElementById('breakdownList');
            breakdownList.innerHTML = '';

            data.breakdown.forEach(item => {
                const li = document.createElement('li');
                const valueColor = item.value >= 0 ? '#10b981' : '#ef4444';
                li.innerHTML = `
                <span>${item.factor}</span>
                <span style="color: ${valueColor}; font-weight: 700;">
                    ${item.value >= 0 ? '+' : ''}${item.value}%
                </span>
            `;
                breakdownList.appendChild(li);
            });

            document.getElementById('breakdown').style.display = 'block';
        }
    }

    // Accept suggestion and fill rent field
    function acceptSuggestion() {
        document.getElementById('rent').value = suggestedRentValue;

        // Auto-fill deposit (1 month rent)
        document.getElementById('deposit').value = suggestedRentValue;

        // Visual feedback
        const rentInput = document.getElementById('rent');
        rentInput.style.background = '#d1fae5';
        rentInput.style.transition = 'background 0.5s';
        setTimeout(() => {
            rentInput.style.background = '';
        }, 1500);

        // Show success message
        showNotification('Suggested rent applied successfully!', 'success');

        // Close after delay
        setTimeout(() => {
            closeSuggestion();
        }, 2000);
    }

    // Close suggestion box
    function closeSuggestion() {
        document.getElementById('rentSuggestion').style.display = 'none';
    }

    // Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.textContent = message;

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6'
        };

        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${colors[type] || colors.info};
        color: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        font-weight: 700;
        font-size: 1rem;
        animation: slideInRight 0.4s ease;
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.4s ease';
            setTimeout(() => notification.remove(), 400);
        }, 3000);
    }

    // Save draft function
    function saveDraft() {
        alert('Draft save functionality - to be implemented');
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
    document.head.appendChild(style);
</script>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>