<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Clue Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: inherit;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .secondary-btn {
            background-color: #2196f3;
        }
        .secondary-btn:hover {
            background-color: #1976d2;
        }
        .result-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .url-box {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 15px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
        }
        .copy-btn {
            background-color: #6c757d;
            font-size: 14px;
            padding: 8px 16px;
        }
        .copy-btn:hover {
            background-color: #5a6268;
        }
        .preview-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #e3f2fd;
            border-radius: 5px;
            border-left: 4px solid #2196f3;
        }
        .hidden {
            display: none;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        .optional-fields {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .optional-fields h4 {
            margin-top: 0;
            color: #856404;
        }
        .indicator-label-group {
            display: flex;
            align-items: center;
            justify-content: space-between; /* Pushes label and button apart */
            margin-bottom: 5px; /* Space below the label/button line */
        }
        .indicator-label-group label {
            margin-bottom: 0; /* Override default label margin */
        }
        .indicator-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Space between indicator inputs */
        }
        .indicator-group input {
            flex: 1 1 calc(50% - 10px); /* Two columns, adjust for gap */
            min-width: 150px; /* Minimum width for each input */
        }
        /* For 5 inputs, make them fit roughly 2-3 per line */
        @media (min-width: 600px) {
            .indicator-group input {
                flex: 1 1 calc(33.33% - 10px); /* Three columns */
            }
        }
        .add-indicator-btn {
            background-color: #6c757d;
            padding: 5px 10px;
            font-size: 14px;
            margin-right: 0; /* Remove default button margin */
        }
        .add-indicator-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Create Your Clue Game</h1>
        <p>Create a custom guessing game by entering your clue and answer below. The system will generate a shareable link that others can use to play your game.</p>
        
        <form id="gameForm">
            <div class="form-group">
                <label for="clue">🔍 Clue (Required):</label>
                <textarea id="clue" 
                         placeholder="Enter your clue here... (e.g., 'I am tall when I'm young and short when I'm old. What am I?')"
                         required></textarea>
            </div>
            
            <div class="form-group">
                <label for="answer">✅ Answer (Required):</label>
                <input type="text" 
                       id="answer" 
                       placeholder="Enter the correct answer... (e.g., 'candle')"
                       required>
            </div>
            
            <div class="optional-fields">
                <h4>📚 Optional Fields</h4>
                <p>These fields are optional but can provide additional context for crossword-style clues:</p>
                
                <div class="form-group">
                    <label for="definition">Definition:</label>
                    <input type="text" 
                           id="definition" 
                           placeholder="The definition part of your clue">
                </div>
                
                <div class="form-group">
                    <label for="fodder">Fodder:</label>
                    <input type="text" 
                           id="fodder" 
                           placeholder="The fodder part of your clue">
                </div>

                <div class="form-group">
                    <div class="indicator-label-group">
                        <label>Indicators:</label>
                        <button type="button" class="add-indicator-btn" onclick="addIndicatorField()">+ Add Indicator</button>
                    </div>
                    <div class="indicator-group" id="indicator-group">
                        <input type="text" id="indicator1" placeholder="Indicator 1">
                        <input type="text" id="indicator2" placeholder="Indicator 2">
                        <input type="text" id="indicator3" placeholder="Indicator 3">
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="generateGame()">🚀 Generate Game Link</button>
            <button type="button" onclick="clearForm()" style="background-color: #dc3545;">🗑️ Clear Form</button>
        </form>
        
        <div id="resultSection" class="result-section hidden">
            <h3>🎉 Your Game is Ready!</h3>
            
            <div class="preview-section">
                <h4>📋 Game Preview:</h4>
                <p><strong>Clue:</strong> <span id="previewClue"></span></p>
                <p><strong>Answer:</strong> <span id="previewAnswer"></span></p>
                <div id="previewOptional"></div>
            </div>
            
            <h4>🔗 Shareable Game Link:</h4>
            <div class="url-box" id="gameUrl"></div>
            
            <button type="button" onclick="copyToClipboard()" class="copy-btn">📋 Copy Link</button>
            <button type="button" onclick="openGame()" class="secondary-btn">🎮 Test Game</button>
            
            <div id="copySuccess" class="success-message hidden">
                ✅ Link copied to clipboard!
            </div>
        </div>
    </div>

    <script>
        // Function to add a new indicator field
        function addIndicatorField() {
            const indicatorGroup = document.getElementById('indicator-group');
            const currentCount = indicatorGroup.querySelectorAll('input[id^="indicator"]').length;
            const newIdNum = currentCount + 1;

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.id = 'indicator' + newIdNum;
            newInput.placeholder = 'Indicator ' + newIdNum;
            
            indicatorGroup.appendChild(newInput);
        }

        function generateGame() {
            // Get form values
            const clue = document.getElementById('clue').value.trim();
            const answer = document.getElementById('answer').value.trim();
            const definition = document.getElementById('definition').value.trim(); // Changed from wordplay
            const fodder = document.getElementById('fodder').value.trim();     
            
            // Collect indicators dynamically
            const indicators = [];
            const indicatorInputs = document.querySelectorAll('#indicator-group input[id^="indicator"]');
            indicatorInputs.forEach(input => {
                const indicatorValue = input.value.trim();
                if (indicatorValue) {
                    indicators.push(indicatorValue);
                }
            });

            // Validate required fields
            if (!clue || !answer) {
                alert('Please fill in both the clue and answer fields.');
                return;
            }
            
            // Create game data object
            const gameData = {
                clue: clue,
                answer: answer
            };
            
            // Add optional fields if they have values
            if (definition) { // Changed from wordplay
                gameData.definition = definition; // Changed from wordplay
            }
            if (fodder) {
                gameData.fodder = fodder;
            }
            if (indicators.length > 0) {
                gameData.indicators = indicators;
            }
            
            // Convert to JSON string
            const jsonString = JSON.stringify(gameData);
            
            // Base64 encode the JSON
            const base64Data = btoa(jsonString);
            
            // Get current page URL without parameters
            const currentUrl = window.location.origin + window.location.pathname;
            const gamePageUrl = currentUrl.replace('admin.php', ''); 
            
            // Generate the full game URL
            const fullGameUrl = gamePageUrl + '?data=' + base64Data;
            
            // Update preview section
            document.getElementById('previewClue').textContent = clue;
            document.getElementById('previewAnswer').textContent = answer;
            
            // Update optional preview
            let optionalHtml = '';
            if (definition) { // Changed from wordplay
                optionalHtml += '<p><strong>Definition:</strong> ' + escapeHtml(definition) + '</p>'; // Changed from wordplay
            }
            if (fodder) {
                optionalHtml += '<p><strong>Fodder:</strong> ' + escapeHtml(fodder) + '</p>';
            }
            if (indicators.length > 0) {
                optionalHtml += '<p><strong>Indicators:</strong> ' + escapeHtml(indicators.join(', ')) + '</p>';
            }
            document.getElementById('previewOptional').innerHTML = optionalHtml;
            
            // Display the URL
            document.getElementById('gameUrl').textContent = fullGameUrl;
            
            // Show result section
            document.getElementById('resultSection').classList.remove('hidden');
            
            // Scroll to result
            document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth' });
        }
        
        function copyToClipboard() {
            const urlText = document.getElementById('gameUrl').textContent;
            
            // Create temporary textarea to copy text
            const tempTextarea = document.createElement('textarea');
            tempTextarea.value = urlText;
            document.body.appendChild(tempTextarea);
            tempTextarea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextarea);
            
            // Show success message
            const successMsg = document.getElementById('copySuccess');
            successMsg.classList.remove('hidden');
            setTimeout(() => {
                successMsg.classList.add('hidden');
            }, 3000);
        }
        
        function openGame() {
            const gameUrl = document.getElementById('gameUrl').textContent;
            window.open(gameUrl, '_blank');
        }
        
        function clearForm() {
            if (confirm('Are you sure you want to clear all fields?')) {
                document.getElementById('gameForm').reset();
                document.getElementById('resultSection').classList.add('hidden');
                
                // Remove all dynamically added indicator fields, keeping only the initial 3
                const indicatorGroup = document.getElementById('indicator-group');
                let currentIndicators = indicatorGroup.querySelectorAll('input[id^="indicator"]');
                for (let i = currentIndicators.length - 1; i >= 3; i--) { // Start from the last and go down to the 4th
                    indicatorGroup.removeChild(currentIndicators[i]);
                }
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Auto-generate when Enter is pressed in answer field
        document.getElementById('answer').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                generateGame();
            }
        });
        
        // Example data for demonstration
        function loadExample() {
            document.getElementById('clue').value = 'I am tall when I\'m young and short when I\'m old. What am I?';
            document.getElementById('answer').value = 'candle';
            document.getElementById('definition').value = 'A source of light (e.g., for a candle)'; // Changed from wordplay
            document.getElementById('fodder').value = 'wax';
            
            // Clear existing dynamic indicators before loading example
            const indicatorGroup = document.getElementById('indicator-group');
            let currentIndicators = indicatorGroup.querySelectorAll('input[id^="indicator"]');
            for (let i = currentIndicators.length - 1; i >= 3; i--) {
                indicatorGroup.removeChild(currentIndicators[i]);
            }

            // Populate initial indicators
            document.getElementById('indicator1').value = 'tall';
            document.getElementById('indicator2').value = 'short';
            document.getElementById('indicator3').value = 'old';
        }
        
        // Add example button on load
        window.onload = function() {
            const form = document.getElementById('gameForm');
            const exampleBtn = document.createElement('button');
            exampleBtn.type = 'button';
            exampleBtn.textContent = '💡 Load Example';
            exampleBtn.style.backgroundColor = '#17a2b8';
            exampleBtn.onclick = loadExample;
            exampleBtn.onmouseover = function() { this.style.backgroundColor = '#138496'; };
            exampleBtn.onmouseout = function() { this.style.backgroundColor = '#17a2b8'; };
            
            // Find a good place to insert the button, e.g., before the generate button
            const generateButton = document.querySelector('button[onclick="generateGame()"]');
            if (generateButton) {
                generateButton.parentNode.insertBefore(exampleBtn, generateButton);
            } else {
                form.appendChild(exampleBtn); // Fallback
            }
        };
    </script>
</body>
</html>