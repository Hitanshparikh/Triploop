<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();

$tripId = $_GET['trip_id'] ?? 1;

// Demo Invoice Data — replace with DB queries
$invoice = [
    'id'          => 'INV-xyz-30290',
    'trip_name'   => 'Trip to Europe Adventure',
    'trip_dates'  => 'May 35 – Jan 05, 2025',
    'cities'      => '4 cities',
    'created_by'  => 'James',
    'generated'   => 'May 20, 2025',
    'status'      => 'pending',   // pending | paid | overdue
    'budget_total'=> 20000,
    'travelers'   => ['James', 'Arjun', 'Jerry', 'Cristina'],
    'items'       => [
        ['category'=>'hotel',   'description'=>'Hotel booking Paris',          'qty'=>'3 nights', 'unit_cost'=>3000,  'amount'=>9000],
        ['category'=>'travel',  'description'=>'Flight bookings (DEL → PAR)',  'qty'=>'1',        'unit_cost'=>12000, 'amount'=>12000],
    ],
    'tax_rate'    => 5,
    'discount'    => 50,
];

$subtotal   = array_sum(array_column($invoice['items'], 'amount'));
$tax        = round($subtotal * $invoice['tax_rate'] / 100, 2);
$grand      = $subtotal + $tax - $invoice['discount'];
$spent      = $grand;
$remaining  = $invoice['budget_total'] - $spent;

$statusColors = ['pending'=>'#F59E0B','paid'=>'#10B981','overdue'=>'#EF4444'];
$statusColor  = $statusColors[$invoice['status']] ?? '#94A3B8';

$catIcons = ['hotel'=>'building-2','travel'=>'plane','food'=>'utensils','activities'=>'zap','shopping'=>'shopping-bag','other'=>'circle'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Expense Invoice — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Invoice Layout */
.invoice-layout{display:grid;grid-template-columns:1fr 320px;gap:var(--space-6);align-items:start;}
/* Invoice document */
.invoice-doc{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-2xl);overflow:hidden;}
.invoice-header{background:linear-gradient(135deg,rgba(0,212,255,0.05),rgba(168,85,247,0.05));border-bottom:var(--border-subtle);padding:var(--space-8);}
.invoice-grid-top{display:grid;grid-template-columns:1fr auto;gap:var(--space-6);align-items:start;}
.invoice-id{font-family:var(--font-mono,'monospace');font-size:var(--text-sm);color:var(--accent-cyan);background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.15);padding:4px 12px;border-radius:var(--radius-full);display:inline-block;margin-bottom:var(--space-3);}
/* Status badge */
.status-badge{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);border-radius:var(--radius-full);font-size:var(--text-sm);font-weight:var(--font-semibold);}
/* Traveler avatars */
.traveler-avatars{display:flex;gap:var(--space-2);flex-wrap:wrap;margin-top:var(--space-2);}
.traveler-avatar{width:36px;height:36px;border-radius:50%;background:var(--gradient-cyan);display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:var(--font-bold);color:var(--bg-primary);border:2px solid var(--bg-primary);}
.traveler-name{font-size:var(--text-xs);color:var(--text-secondary);margin-top:4px;text-align:center;}
/* Search toolbar */
.invoice-toolbar{padding:var(--space-4) var(--space-6);border-bottom:var(--border-subtle);display:flex;gap:var(--space-3);align-items:center;flex-wrap:wrap;}
.search-box{position:relative;flex:1;min-width:180px;}
.search-box input{width:100%;padding:var(--space-2) var(--space-4) var(--space-2) 36px;background:var(--bg-elevated);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;}
.search-box input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.toolbar-select{padding:var(--space-2) var(--space-3);background:var(--bg-elevated);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* Items Table */
.invoice-table-wrap{padding:var(--space-6);overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead th{font-size:var(--text-xs);font-weight:var(--font-semibold);color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;padding:var(--space-2) var(--space-4);text-align:left;border-bottom:1px solid rgba(148,163,184,0.1);}
tbody td{padding:var(--space-4);border-bottom:1px solid rgba(148,163,184,0.05);font-size:var(--text-sm);vertical-align:middle;}
tbody tr:hover{background:rgba(0,212,255,0.02);}
tbody tr:last-child td{border-bottom:none;}
.cat-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:var(--font-semibold);}
/* Add row */
.add-item-row td{padding:var(--space-3) var(--space-4);}
.add-item-btn{display:inline-flex;align-items:center;gap:4px;font-size:var(--text-xs);color:var(--accent-cyan);background:none;border:none;cursor:pointer;padding:0;}
/* Totals section */
.invoice-totals{padding:var(--space-6);border-top:var(--border-subtle);background:rgba(0,0,0,0.1);}
.totals-table{margin-left:auto;max-width:320px;}
.total-row{display:flex;justify-content:space-between;align-items:center;padding:var(--space-2) 0;font-size:var(--text-sm);}
.total-row.grand{border-top:1px solid rgba(148,163,184,0.15);margin-top:var(--space-2);padding-top:var(--space-3);font-size:var(--text-lg);font-weight:var(--font-bold);}
/* Actions */
.invoice-actions{padding:var(--space-6);border-top:var(--border-subtle);display:flex;gap:var(--space-3);flex-wrap:wrap;}
/* Right Panel */
.side-panel{display:flex;flex-direction:column;gap:var(--space-5);}
.panel-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-5);}
.panel-title{font-size:var(--text-sm);font-weight:var(--font-semibold);margin-bottom:var(--space-4);display:flex;align-items:center;gap:var(--space-2);}
.panel-title i{width:16px;height:16px;color:var(--accent-cyan);}
.budget-stat{display:flex;justify-content:space-between;align-items:center;padding:var(--space-2) 0;border-bottom:1px solid rgba(148,163,184,0.06);font-size:var(--text-sm);}
.budget-stat:last-child{border-bottom:none;}
.budget-bar{width:100%;height:8px;background:var(--bg-elevated);border-radius:var(--radius-full);overflow:hidden;margin-top:var(--space-3);}
.budget-fill{height:100%;border-radius:var(--radius-full);}
/* Mark paid */
.mark-paid-btn{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:var(--accent-green);}
.mark-paid-btn:hover{background:rgba(16,185,129,0.2);}
/* Print styles */
@media print {
  .app-layout > aside, .invoice-toolbar, .invoice-actions, .side-panel { display:none !important; }
  .main-content { margin-left:0 !important; padding:0 !important; }
  .invoice-layout { grid-template-columns:1fr !important; }
  .invoice-doc { border:none !important; background:white !important; color:black !important; }
}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Page Header -->
  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to My Trips
      </a>
      <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-1);">Expense <span class="text-gradient">Invoice</span></h1>
      <p style="color:var(--text-secondary);"><?= e($invoice['trip_name']) ?> · <?= e($invoice['trip_dates']) ?> · <?= e($invoice['cities']) ?></p>
    </div>
  </div>

  <div class="invoice-layout">

    <!-- INVOICE DOCUMENT -->
    <div class="invoice-doc">

      <!-- Header -->
      <div class="invoice-header">
        <div class="invoice-grid-top">
          <div>
            <div class="invoice-id"><?= e($invoice['id']) ?></div>
            <h2 style="margin:var(--space-2) 0 var(--space-1);"><?= e($invoice['trip_name']) ?></h2>
            <p style="color:var(--text-secondary);font-size:var(--text-sm);"><?= e($invoice['trip_dates']) ?> · <?= e($invoice['cities']) ?></p>
            <p style="color:var(--text-muted);font-size:var(--text-xs);margin-top:var(--space-1);">Created by <?= e($invoice['created_by']) ?></p>
          </div>
          <div style="text-align:right;">
            <div style="margin-bottom:var(--space-3);">
              <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:4px;">Generated date</p>
              <p style="font-weight:var(--font-semibold);"><?= e($invoice['generated']) ?></p>
            </div>
            <span class="status-badge" style="background:<?= $statusColor ?>20;color:<?= $statusColor ?>;border:1px solid <?= $statusColor ?>30;" id="statusBadge">
              <i data-lucide="<?= $invoice['status']==='paid'?'check-circle':'clock' ?>" style="width:12px;height:12px;"></i>
              Payment status — <?= ucfirst($invoice['status']) ?>
            </span>
          </div>
        </div>

        <!-- Travelers -->
        <div style="margin-top:var(--space-6);">
          <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-3);text-transform:uppercase;letter-spacing:0.06em;">Traveler Details</p>
          <div style="display:flex;gap:var(--space-5);flex-wrap:wrap;">
            <?php $colors=['#00D4FF','#A855F7','#FF6B35','#10B981','#F59E0B','#EC4899']; ?>
            <?php foreach($invoice['travelers'] as $i => $t): ?>
            <div style="text-align:center;">
              <div class="traveler-avatar" style="background:<?= $colors[$i % count($colors)] ?>;margin:0 auto;">
                <?= strtoupper(substr($t,0,1)) ?>
              </div>
              <div class="traveler-name"><?= e($t) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Search Toolbar -->
      <div class="invoice-toolbar">
        <div class="search-box">
          <span class="search-icon"><i data-lucide="search" style="width:12px;height:12px;"></i></span>
          <input type="text" placeholder="Search invoices..." oninput="filterRows(this.value)">
        </div>
        <select class="toolbar-select">
          <option>Filter...</option>
          <option>Hotel</option>
          <option>Travel</option>
          <option>Food</option>
          <option>Activities</option>
        </select>
        <select class="toolbar-select">
          <option>Sort...</option>
          <option>By amount ↓</option>
          <option>By category</option>
        </select>
      </div>

      <!-- Items Table -->
      <div class="invoice-table-wrap">
        <table id="invoiceTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Category</th>
              <th>Description</th>
              <th>Qty / Details</th>
              <th>Unit Cost</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            <?php foreach($invoice['items'] as $i => $item):
              $catColors=['hotel'=>'#00D4FF','travel'=>'#A855F7','food'=>'#F59E0B','activities'=>'#FF6B35','shopping'=>'#10B981','other'=>'#94A3B8'];
              $cc = $catColors[$item['category']] ?? '#94A3B8';
              $ci = $catIcons[$item['category']] ?? 'circle';
            ?>
            <tr class="item-row">
              <td style="color:var(--text-muted);font-weight:bold;"><?= $i+1 ?></td>
              <td>
                <span class="cat-badge" style="background:<?= $cc ?>15;color:<?= $cc ?>;">
                  <i data-lucide="<?= $ci ?>" style="width:10px;height:10px;"></i>
                  <?= ucfirst($item['category']) ?>
                </span>
              </td>
              <td><?= e($item['description']) ?></td>
              <td style="color:var(--text-secondary);"><?= e($item['qty']) ?></td>
              <td style="color:var(--text-secondary);">$<?= number_format($item['unit_cost']) ?></td>
              <td style="font-weight:var(--font-semibold);color:var(--accent-cyan);">$<?= number_format($item['amount']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="add-item-row">
              <td colspan="6">
                <button class="add-item-btn" onclick="addItemRow()">
                  <i data-lucide="plus" style="width:12px;height:12px;"></i> Add item
                </button>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Totals -->
      <div class="invoice-totals">
        <div class="totals-table">
          <div class="total-row">
            <span style="color:var(--text-secondary);">Subtotal</span>
            <span>$<?= number_format($subtotal) ?></span>
          </div>
          <div class="total-row">
            <span style="color:var(--text-secondary);">Tax (<?= $invoice['tax_rate'] ?>%)</span>
            <span style="color:var(--accent-orange);">+$<?= number_format($tax) ?></span>
          </div>
          <div class="total-row">
            <span style="color:var(--text-secondary);">Discount</span>
            <span style="color:var(--accent-green);">-$<?= number_format($invoice['discount']) ?></span>
          </div>
          <div class="total-row grand">
            <span>Grand Total</span>
            <span class="text-gradient">$<?= number_format($grand) ?></span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="invoice-actions">
        <button class="btn btn-secondary" onclick="window.print()">
          <i data-lucide="download" style="width:15px;height:15px;"></i> Download Invoice
        </button>
        <button class="btn btn-secondary" onclick="exportPDF()">
          <i data-lucide="file-text" style="width:15px;height:15px;"></i> Export as PDF
        </button>
        <?php if($invoice['status'] !== 'paid'): ?>
        <button class="btn mark-paid-btn" onclick="markPaid()">
          <i data-lucide="check-circle" style="width:15px;height:15px;"></i> Mark as paid
        </button>
        <?php else: ?>
        <span class="btn" style="background:rgba(16,185,129,0.1);color:var(--accent-green);border:1px solid rgba(16,185,129,0.2);cursor:default;">
          <i data-lucide="check-circle-2" style="width:15px;height:15px;"></i> Paid
        </span>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="side-panel">

      <!-- Budget Insights -->
      <div class="panel-card">
        <div class="panel-title"><i data-lucide="bar-chart-2"></i> Budget Insights</div>
        <div class="budget-stat">
          <span style="color:var(--text-secondary);">Total Budget</span>
          <span style="font-weight:var(--font-bold);">$<?= number_format($invoice['budget_total']) ?></span>
        </div>
        <div class="budget-stat">
          <span style="color:var(--text-secondary);">Total Spent</span>
          <span style="font-weight:var(--font-bold);color:var(--accent-orange);">$<?= number_format($spent) ?></span>
        </div>
        <div class="budget-stat">
          <span style="color:var(--text-secondary);">Remaining</span>
          <span style="font-weight:var(--font-bold);color:<?= $remaining < 0 ? 'var(--accent-red)' : 'var(--accent-green)' ?>;">
            <?= $remaining < 0 ? '-$'.number_format(abs($remaining)) : '$'.number_format($remaining) ?>
          </span>
        </div>

        <!-- Spend bar -->
        <?php $spentPct = min(100, round($spent / $invoice['budget_total'] * 100)); ?>
        <div style="margin-top:var(--space-4);">
          <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--text-muted);margin-bottom:6px;">
            <span>Spent</span><span><?= $spentPct ?>%</span>
          </div>
          <div class="budget-bar">
            <div class="budget-fill" style="width:<?= $spentPct ?>%;background:<?= $spentPct > 100 ? 'var(--accent-red)' : ($spentPct > 80 ? 'var(--accent-orange)' : 'var(--gradient-cyan)') ?>;"></div>
          </div>
        </div>

        <?php if($remaining < 0): ?>
        <div style="margin-top:var(--space-4);padding:var(--space-3);background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.15);border-radius:var(--radius-lg);font-size:var(--text-xs);color:var(--accent-red);">
          <i data-lucide="alert-triangle" style="width:12px;height:12px;display:inline;"></i>
          Over budget by $<?= number_format(abs($remaining)) ?>
        </div>
        <?php endif; ?>

        <a href="<?= APP_URL ?>/pages/budget.php?trip_id=<?= $tripId ?>" class="btn btn-secondary w-full" style="margin-top:var(--space-4);font-size:var(--text-sm);">
          <i data-lucide="wallet" style="width:14px;height:14px;"></i> View Full Budget
        </a>
      </div>

      <!-- Category Breakdown -->
      <div class="panel-card">
        <div class="panel-title"><i data-lucide="pie-chart"></i> Category Breakdown</div>
        <?php
        $byCategory = [];
        foreach($invoice['items'] as $item) {
            $byCategory[$item['category']] = ($byCategory[$item['category']] ?? 0) + $item['amount'];
        }
        arsort($byCategory);
        $catColors2=['hotel'=>'#00D4FF','travel'=>'#A855F7','food'=>'#F59E0B','activities'=>'#FF6B35','shopping'=>'#10B981','other'=>'#94A3B8'];
        ?>
        <?php foreach($byCategory as $cat => $amt): $cc2=$catColors2[$cat]??'#94A3B8'; $pct2=round($amt/$subtotal*100); ?>
        <div style="margin-bottom:var(--space-3);">
          <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--text-secondary);margin-bottom:4px;">
            <span style="color:<?= $cc2 ?>;">● <?= ucfirst($cat) ?></span>
            <span>$<?= number_format($amt) ?> (<?= $pct2 ?>%)</span>
          </div>
          <div class="budget-bar"><div class="budget-fill" style="width:<?= $pct2 ?>%;background:<?= $cc2 ?>;"></div></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Travelers Split -->
      <div class="panel-card">
        <div class="panel-title"><i data-lucide="users"></i> Per-Person Split</div>
        <?php $perPerson = round($grand / count($invoice['travelers']), 2); ?>
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-4);">Equal split among <?= count($invoice['travelers']) ?> travelers</p>
        <?php foreach($invoice['travelers'] as $i => $t): ?>
        <div class="budget-stat">
          <div style="display:flex;align-items:center;gap:var(--space-2);">
            <div style="width:24px;height:24px;border-radius:50%;background:<?= $colors[$i%count($colors)] ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;color:var(--bg-primary);"><?= strtoupper(substr($t,0,1)) ?></div>
            <span style="font-size:var(--text-sm);"><?= e($t) ?></span>
          </div>
          <span style="font-weight:var(--font-semibold);color:var(--accent-cyan);">$<?= number_format($perPerson) ?></span>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

</main>
</div>

<script>
function filterRows(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#itemsBody .item-row').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function markPaid() {
  if(!confirm('Mark this invoice as paid?')) return;
  const badge = document.getElementById('statusBadge');
  badge.style.background = 'rgba(16,185,129,0.2)';
  badge.style.color = '#10B981';
  badge.style.borderColor = 'rgba(16,185,129,0.3)';
  badge.innerHTML = '<i data-lucide="check-circle" style="width:12px;height:12px;"></i> Payment status — Paid';
  lucide.createIcons();
  // In production: POST to api/invoice.php?action=mark_paid&id=...
}

function exportPDF() {
  window.print();
  // In production: integrate a PDF library (e.g., dompdf)
}

let itemCount = <?= count($invoice['items']) ?>;
function addItemRow() {
  itemCount++;
  const tbody = document.getElementById('itemsBody');
  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.innerHTML = `
    <td style="color:var(--text-muted);font-weight:bold;">${itemCount}</td>
    <td><input class="input-field" style="padding:6px 10px;font-size:var(--text-xs);width:100px;" placeholder="Category"></td>
    <td><input class="input-field" style="padding:6px 10px;font-size:var(--text-xs);width:180px;" placeholder="Description"></td>
    <td><input class="input-field" style="padding:6px 10px;font-size:var(--text-xs);width:80px;" placeholder="Qty"></td>
    <td><input class="input-field" style="padding:6px 10px;font-size:var(--text-xs);width:90px;" type="number" placeholder="Unit Cost" oninput="updateTotal(this)"></td>
    <td style="font-weight:bold;color:var(--accent-cyan);" class="row-amount">$0</td>
  `;
  tbody.appendChild(tr);
}

function updateTotal(input) {
  const row = input.closest('tr');
  const amt = parseFloat(input.value) || 0;
  row.querySelector('.row-amount').textContent = '$' + amt.toLocaleString();
}

lucide.createIcons();
</script>
</body>
</html>
