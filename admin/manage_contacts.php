<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Quản lý liên hệ - Gerrapp Admin';
$currentPage = 'admin-contacts';
$bodyClass = 'admin-page';

// Add Bootstrap script and inline scripts to header
$extraHeadContent = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>';

// Define inline scripts
$inlineScripts = <<<SCRIPT
document.addEventListener("DOMContentLoaded", function() {
    const contactModal = document.getElementById("contactModal");
    const replyForm = document.getElementById("replyForm");
    const contactIdInput = document.getElementById("contactId");
    const contactSubject = document.getElementById("contactSubject");
    const contactMessage = document.getElementById("contactMessage");
    const contactDate = document.getElementById("contactDate");
    const previousReplySection = document.getElementById("previousReplySection");
    const previousReply = document.getElementById("previousReply");
    const previousReplyDate = document.getElementById("previousReplyDate");
    const replyMessage = document.getElementById("replyMessage");
    
    if (!contactModal || !replyForm) {
        console.error("Required elements not found");
        return;
    }

    const bsModal = new bootstrap.Modal(contactModal);
    
    // Add modal cleanup handler with additional cleanup
    contactModal.addEventListener("hidden.bs.modal", function() {
        replyForm.reset();
        previousReplySection.style.display = "none";
        // Remove any lingering backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Define handleContactView function
    async function handleContactView() {
        const id = this.dataset.id;
        try {
            // Update status to read
            await fetch("forms/update_contact_status.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ 
                    id: id, 
                    status: "read"
                })
            });

            // Fetch contact details
            const response = await fetch("forms/get_contact.php?id=" + id);
            const data = await response.json();
            
            contactIdInput.value = id;
            contactSubject.textContent = data.subject;
            contactMessage.textContent = data.message;
            contactDate.textContent = data.created_at;
            
            if (data.reply_message) {
                previousReply.textContent = data.reply_message;
                previousReplyDate.textContent = data.replied_at;
                previousReplySection.style.display = "block";
            } else {
                previousReplySection.style.display = "none";
            }
            
            bsModal.show();
            
            // Update status badge
            const statusBadge = this.closest("tr").querySelector(".status-badge");
            if (statusBadge && statusBadge.textContent.trim() === "Mới") {
                statusBadge.className = "badge bg-info status-badge";
                statusBadge.textContent = "Đã đọc";
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Có lỗi xảy ra khi tải thông tin liên hệ");
        }
    }

    // Attach event listeners to view buttons
    document.querySelectorAll(".view-contact").forEach(button => {
        button.addEventListener("click", handleContactView);
    });

    // Handle reply form submission
    replyForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        const id = contactIdInput.value;
        const reply = replyMessage.value;
        
        try {
            const response = await fetch("forms/reply_contact.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id, reply })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert("Phản hồi đã được gửi thành công");
                replyMessage.value = "";
                bsModal.hide();
                
                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 300);
            } else {
                throw new Error(data.message || "Failed to send reply");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Có lỗi xảy ra khi gửi phản hồi");
        }
    });
});
SCRIPT;

// Add to header
$extraHeadContent .= "<script>{$inlineScripts}</script>";

require_once '../config/database.php';
include '../includes/header.php';

// Get contacts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$total_records = $conn->query("SELECT COUNT(*) FROM contact_message")->fetch_row()[0];
$total_pages = ceil($total_records / $per_page);

// Get contacts for current page
$contacts = $conn->query("
    SELECT c.*, u.full_name, u.email 
    FROM contact_message c 
    LEFT JOIN users u ON c.user_id = u.id 
    ORDER BY c.id ASC 
    LIMIT $offset, $per_page
");
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Quản lý liên hệ</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li class="current">Quản lý liên hệ</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Danh sách liên hệ</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Người gửi</th>
                                            <th>Email</th>
                                            <th>Tiêu đề</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày gửi</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($contact = $contacts->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $contact['id']; ?></td>
                                            <td>
                                                <?php 
                                                echo $contact['full_name'] 
                                                    ? htmlspecialchars($contact['full_name']) 
                                                    : htmlspecialchars($contact['name']); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo $contact['email'] 
                                                    ? htmlspecialchars($contact['email']) 
                                                    : htmlspecialchars($contact['contact_email']); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($contact['status']) {
                                                        'new' => 'danger',
                                                        'read' => 'info',
                                                        'replied' => 'success',
                                                        default => 'secondary'
                                                    };
                                                ?> status-badge">
                                                    <?php 
                                                    echo match($contact['status']) {
                                                        'new' => 'Mới',
                                                        'read' => 'Đã đọc',
                                                        'replied' => 'Đã phản hồi',
                                                        default => $contact['status']
                                                    }; 
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm view-contact"
                                                        data-id="<?php echo $contact['id']; ?>"
                                                        style="background-color: var(--accent-color); color: var(--contrast-color);">
                                                    Chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Contact View Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết liên hệ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="contactId">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <p id="contactSubject" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <p id="contactMessage" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày gửi</label>
                    <p id="contactDate" class="form-control-plaintext"></p>
                </div>
                <div id="previousReplySection" style="display:none">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Phản hồi trước đó</h6>
                            <p id="previousReply" class="card-text"></p>
                            <small id="previousReplyDate" class="text-muted"></small>
                        </div>
                    </div>
                </div>
                <form id="replyForm">
                    <div class="mb-3">
                        <label class="form-label">Phản hồi</label>
                        <textarea id="replyMessage" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($conn)) $conn->close();
include '../includes/footer.php'; 
?>