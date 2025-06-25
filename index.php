<?php
session_start();

// Initialize variables
$clue = '';
$correctAnswer = '';
$gameData = null;
$gameId = null;
$currentGame = null;
$userAnswer = '';
$showResult = false;
$triggerFlash = false; // Flag to trigger the flash effect

// Check if data parameter exists
if (isset($_GET['data'])) {
    try {
        // Decode base64
        $decodedJson = base64_decode($_GET['data']);

        if ($decodedJson === false) {
            throw new Exception("Invalid base64 encoding");
        }

        // Decode JSON
        $gameData = json_decode($decodedJson, true);

        if ($gameData === null) {
            throw new Exception("Invalid JSON data");
        }

        // Validate required fields
        if (!isset($gameData['clue']) || !isset($gameData['answer'])) {
            throw new Exception("Missing required fields: 'clue' and 'answer'");
        }

        $clue = htmlspecialchars($gameData['clue']);
        $correctAnswer = $gameData['answer'];

        // Create unique ID for this game data
        $gameId = md5($_GET['data']);

        // Initialize session data for this game if it doesn't exist
        if (!isset($_SESSION['games'][$gameId])) {
            $_SESSION['games'][$gameId] = [
                'attempts' => [],
                'solved' => false
            ];
        }

        $currentGame = &$_SESSION['games'][$gameId];

        // Check if user submitted an answer
        $userAnswer = isset($_POST['user_answer']) ? trim($_POST['user_answer']) : '';
        $showResult = !empty($userAnswer);

        // Process the answer if submitted and game is not already solved
        if ($showResult && !$currentGame['solved']) {
            $isCorrect = strcasecmp($userAnswer, $correctAnswer) === 0;

            // Add attempt to history
            $currentGame['attempts'][] = [
                'answer' => $userAnswer,
                'correct' => $isCorrect,
                'timestamp' => date('H:i:s')
            ];

            if ($isCorrect) {
                $currentGame['solved'] = true;
            } else {
                 // Trigger the flash effect on incorrect guess
                $triggerFlash = true;
            }
        }

        // Reset game if requested
        if (isset($_POST['reset_game'])) {
            unset($_SESSION['games'][$gameId]);
            // Redirect to clear POST data and session state cleanly
            header("Location: " . $_SERVER['PHP_SELF'] . "?data=" . urlencode($_GET['data']));
            exit;
        }

    } catch (Exception $e) {
        // Handle errors during data processing
        $clue = null; // Indicate error state
        $errorMessage = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clue Guessing Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
            transition: background-color 0.1s ease; /* Smooth transition for flash */
        }
         body.flash-red {
            background-color: #f8d7da; /* Light red background */
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .clue {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px; /* Space between buttons */
        }
        button:hover {
            background-color: #45a049;
        }
         .secondary-btn { /* Style for the Share button */
            background-color: #007bff;
        }
        .secondary-btn:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .correct {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .incorrect {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .attempts {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #6c757d;
        }
        .attempts h4 {
            margin-top: 0;
            color: #495057;
        }
        .attempt-item {
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .attempt-item:last-child {
            border-bottom: none;
        }
        .attempt-number {
            font-weight: bold;
            color: #6c757d;
        }
        .game-over {
            text-align: center;
            padding: 20px;
        }
        .game-over input, .game-over button:not(#shareButton):not([name="reset_game"]) { /* Disable input and submit button */
            opacity: 0.5;
            pointer-events: none;
        }
        .success-message { /* Style for copy success message */
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
         .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧩 Clue Guessing Game</h1>

        <?php if (isset($errorMessage)): ?>
            <div class='result error'><?php echo $errorMessage; ?></div>
        <?php elseif ($clue !== null): // Only display game if data was processed successfully ?>

            <div class="clue">
                <h3>🔍 Your Clue:</h3>
                <p id="theClueText"><?php echo $clue; ?></p> <!-- Added ID for easy JS access -->
            </div>

            <?php if (!empty($currentGame['attempts'])): ?>
                <div class="attempts">
                    <h4>📝 Your Attempts (<?php echo count($currentGame['attempts']); ?>):</h4>
                    <?php foreach ($currentGame['attempts'] as $index => $attempt): ?>
                        <div class="attempt-item">
                            <span class="attempt-number">#<?php echo $index + 1; ?>:</span>
                            "<?php echo htmlspecialchars($attempt['answer']); ?>"
                            <?php if ($attempt['correct']): ?>
                                <span style="color: #28a745;">✓ Correct!</span>
                            <?php else: ?>
                                <span style="color: #dc3545;">✗ Try again</span>
                            <?php endif; ?>
                            <small style="color: #6c757d; float: right;"><?php echo $attempt['timestamp']; ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentGame['solved']): ?>
                <div class="result correct game-over">
                    <h3>🎉 Congratulations!</h3>
                    <p>You solved it in <?php echo count($currentGame['attempts']); ?> attempt<?php echo count($currentGame['attempts']) > 1 ? 's' : ''; ?>!</p>

                    <button type="button" id="shareButton" class="secondary-btn">📋 Share Clue</button> <!-- Share Button -->

                    <form method="POST" style="display: inline-block; margin-left: 10px;"> <!-- Align buttons -->
                        <button type="submit" name="reset_game" style="background-color: #007bff;">🔄 Play Again</button>
                    </form>

                    <div id="shareSuccess" class="success-message hidden">
                         Link copied to clipboard! <!-- Message updated by JS -->
                    </div>

                </div>
            <?php endif; ?>

            <div class="<?php echo $currentGame['solved'] ? 'game-over' : ''; ?>">
                <form method="POST">
                    <div class="form-group">
                        <label for="user_answer">Your Answer:</label>
                        <input type="text"
                               id="user_answer"
                               name="user_answer"
                               placeholder="Enter your guess here..."
                               value=""
                               <?php echo $currentGame['solved'] ? 'disabled' : 'required'; ?>>
                    </div>
                    <button type="submit" <?php echo $currentGame['solved'] ? 'disabled' : ''; ?>>
                        Submit Answer
                    </button>
                </form>
            </div>

            <?php
            // Show result for the most recent attempt (but don't reveal answer)
            if ($showResult && !$currentGame['solved']) {
                $lastAttempt = end($currentGame['attempts']);
                if (!$lastAttempt['correct']) {
                    echo "<div class='result incorrect'>❌ That's not correct. Keep trying!</div>";
                }
            }
            ?>

        <?php else: // No data provided or error ?>
            <div class="error">
                <h3>No Game Data Provided</h3>
                <p>To play the game, you need to provide a base64-encoded JSON string in the URL.</p>
                <p><strong>URL Format:</strong> <code>?data=YOUR_BASE64_ENCODED_JSON</code></p>
                <p><strong>JSON Format:</strong></p>
                <pre>{
  "clue": "Your clue text here",
  "answer": "The correct answer"
}</pre>

                <h4>Example:</h4>
                <?php
                // Create example
                $exampleData = [
                    "clue" => "I am tall when I'm young and short when I'm old. What am I?",
                    "answer" => "candle"
                ];
                $exampleJson = json_encode($exampleData);
                $exampleBase64 = base64_encode($exampleJson);
                $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                ?>
                <p><a href="<?php echo $currentUrl; ?>?data=<?php echo $exampleBase64; ?>">Try this example</a></p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript for the red flash effect
        <?php if ($triggerFlash): ?>
        document.body.classList.add('flash-red');
        setTimeout(() => {
            document.body.classList.remove('flash-red');
        }, 300); // Flash for 300ms
        <?php endif; ?>

        // JavaScript for the Share button
        const shareButton = document.getElementById('shareButton');
        const clueTextElement = document.getElementById('theClueText');
        const shareSuccessMessage = document.getElementById('shareSuccess');

        if (shareButton && clueTextElement) {
            shareButton.addEventListener('click', () => {
                const clue = clueTextElement.textContent;
                const textToCopy = clue + ' ✅'; // Clue + green checkmark

                // Use the Clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Success
                        shareSuccessMessage.textContent = '✅ Clue copied to clipboard!';
                        shareSuccessMessage.classList.remove('hidden');
                        setTimeout(() => {
                            shareSuccessMessage.classList.add('hidden');
                        }, 3000); // Hide message after 3 seconds
                    }).catch(err => {
                        // Error
                        console.error('Failed to copy text: ', err);
                        shareSuccessMessage.textContent = '❌ Failed to copy clue.';
                        shareSuccessMessage.classList.remove('hidden');
                         setTimeout(() => {
                            shareSuccessMessage.classList.add('hidden');
                        }, 3000);
                    });
                } else {
                    // Fallback for browsers that don't support Clipboard API (less common now)
                    console.warn('Clipboard API not available. Using fallback.');
                    try {
                         const tempTextarea = document.createElement('textarea');
                         tempTextarea.value = textToCopy;
                         document.body.appendChild(tempTextarea);
                         tempTextarea.select();
                         document.execCommand('copy');
                         document.body.removeChild(tempTextarea);

                         shareSuccessMessage.textContent = '✅ Clue copied to clipboard (fallback)!';
                         shareSuccessMessage.classList.remove('hidden');
                         setTimeout(() => {
                             shareSuccessMessage.classList.add('hidden');
                         }, 3000);
                    } catch (err) {
                         console.error('Fallback copy failed: ', err);
                         shareSuccessMessage.textContent = '❌ Failed to copy clue.';
                         shareSuccessMessage.classList.remove('hidden');
                         setTimeout(() => {
                             shareSuccessMessage.classList.add('hidden');
                         }, 3000);
                    }
                }
            });
        }
    </script>
</body>
</html>