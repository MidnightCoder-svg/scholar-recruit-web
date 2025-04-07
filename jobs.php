
<?php
require_once 'config.php';
$page_title = "Browse Jobs";
include 'includes/header.php';

// Handle filtering
$where_clauses = ["j.deadline >= CURRENT_DATE"];
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%{$_GET['search']}%";
    $where_clauses[] = "(j.title LIKE ? OR j.description LIKE ?)";
    array_push($params, $search, $search);
    $types .= "ss";
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $where_clauses[] = "j.type = ?";
    array_push($params, $_GET['type']);
    $types .= "s";
}

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $where_clauses[] = "j.location LIKE ?";
    array_push($params, "%{$_GET['location']}%");
    $types .= "s";
}

// Build the query
$where_clause = implode(" AND ", $where_clauses);
$sql = "SELECT j.*, u.name as company_name 
        FROM jobs j 
        JOIN users u ON j.company_id = u.id 
        WHERE $where_clause 
        ORDER BY j.posted_date DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get job types for filter
$types_sql = "SELECT DISTINCT type FROM jobs ORDER BY type";
$types_result = $conn->query($types_sql);

// Get locations for filter
$locations_sql = "SELECT DISTINCT location FROM jobs ORDER BY location";
$locations_result = $conn->query($locations_sql);
?>

<div class="container py-5">
    <h1 class="mb-4">Browse Job Opportunities</h1>
    
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="jobs.php" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Job Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <?php while ($type = $types_result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($type['type']) ?>" 
                                            <?= (isset($_GET['type']) && $_GET['type'] == $type['type']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['type']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">All Locations</option>
                                <?php while ($location = $locations_result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($location['location']) ?>"
                                            <?= (isset($_GET['location']) && $_GET['location'] == $location['location']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($location['location']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <?php if (!empty($_GET)): ?>
                            <a href="jobs.php" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Job Listings -->
        <div class="col-lg-9">
            <?php if ($result->num_rows > 0): ?>
                <p>Showing <?= $result->num_rows ?> job opportunities</p>
                
                <?php while ($job = $result->fetch_assoc()): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">
                                    <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </a>
                                </h5>
                                <span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($job['company_name']) ?></h6>
                            
                            <div class="mb-3 d-flex gap-3">
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?>
                                </div>
                                <?php if (!empty($job['salary'])): ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-cash"></i> <?= htmlspecialchars($job['salary']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar"></i> Apply by: <?= htmlspecialchars($job['deadline']) ?>
                                </div>
                            </div>
                            
                            <p class="card-text">
                                <?= htmlspecialchars(substr($job['description'], 0, 200)) ?>...
                            </p>
                            
                            <?php if (!empty($job['skills'])): ?>
                                <div class="mb-3">
                                    <h6 class="card-subtitle mb-2">Skills:</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach (explode(',', $job['skills']) as $skill): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars(trim($skill)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary mt-2">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    No job opportunities found matching your criteria. Try adjusting your filters.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
