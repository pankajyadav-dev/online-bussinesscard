<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage("Invalid card ID.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT uc.*, cd.name as design_name, cd.template_path, cd.category
    FROM user_cards uc 
    JOIN card_designs cd ON uc.design_id = cd.id 
    WHERE uc.id = ?
");
$stmt->execute([$card_id]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card = $stmt->fetch();

$is_owner = isLoggedIn() && $card['user_id'] == $_SESSION['user_id'];

$is_public_view = isset($_GET['share']) && $_GET['share'] == true;

if(!$is_owner && !$is_public_view) {
    setMessage("You don't have permission to view this card.", "error");
    header("Location: /pages/auth/login.php");
    exit;
}

$custom_fields = json_decode($card['custom_fields'], true);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$card['user_id']]);
$user = $stmt->fetch();
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold"><?php echo $card['card_name']; ?></h2>
        
        <?php if($is_owner): ?>
            <div>
                <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
                <a href="/pages/cards/edit.php?id=<?php echo $card_id; ?>" class="ml-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-edit mr-1"></i> Edit Card
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-2/3 mx-auto">
                <div class="border rounded-lg overflow-hidden shadow-lg">
                    <?php 
                    $bg_color = "bg-blue-600";
                    if($card['category'] == 'Creative') {
                        $bg_color = "bg-pink-500";
                    } elseif($card['category'] == 'Minimalist') {
                        $bg_color = "bg-gray-800";
                    } elseif($card['category'] == 'Corporate') {
                        $bg_color = "bg-gray-600";
                    }
                    ?>
                    
                    <div class="h-56 <?php echo $bg_color; ?> flex flex-col items-center justify-center p-6 text-white">
                        <h2 class="text-2xl font-bold mb-1"><?php echo $custom_fields['name']; ?></h2>
                        <?php if(!empty($custom_fields['job_title'])): ?>
                            <p class="text-lg"><?php echo $custom_fields['job_title']; ?></p>
                        <?php endif; ?>
                        <?php if(!empty($custom_fields['company'])): ?>
                            <p class="text-md mt-2"><?php echo $custom_fields['company']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if(!empty($custom_fields['phone'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['phone']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['email'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['email']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['website'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-globe text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['website']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['address'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['address']; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($is_owner): ?>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">Share Your Business Card</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-bold mb-3">QR Code</h4>
                <div id="qrcode" class="mb-3"></div>
                <button id="downloadQR" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 mt-2">
                    <i class="fas fa-download mr-1"></i> Download QR Code
                </button>
            </div>
            
            <div>
                <h4 class="font-bold mb-3">Share via Email</h4>
                <form id="shareForm" method="POST" action="/pages/cards/share.php">
                    <input type="hidden" name="card_id" value="<?php echo $card_id; ?>">
                    
                    <div class="mb-4">
                        <label for="recipient_email" class="block text-gray-700 font-medium mb-2">Recipient Email</label>
                        <input type="email" id="recipient_email" name="recipient_email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message (Optional)</label>
                        <textarea id="message" name="message" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">I'd like to share my business card with you.</textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-1"></i> Send
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="font-bold mb-3">Direct Link</h4>
            <div class="flex">
                <input type="text" id="share_link" class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $_SERVER['HTTP_HOST'] . '/pages/cards/view.php?id=' . $card_id . '&share=true'; ?>" readonly>
                <button id="copyLink" class="bg-blue-600 text-white py-2 px-4 rounded-r-md hover:bg-blue-700">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo $_SERVER['HTTP_HOST'] . '/pages/cards/view.php?id=' . $card_id . '&share=true'; ?>",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    document.getElementById('copyLink').addEventListener('click', function() {
        var shareLink = document.getElementById('share_link');
        shareLink.select();
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    });
    
    document.getElementById('downloadQR').addEventListener('click', function() {
        var img = document.querySelector('#qrcode img');
        var link = document.createElement('a');
        link.download = 'business-card-qr.png';
        link.href = img.src;
        link.click();
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?> 