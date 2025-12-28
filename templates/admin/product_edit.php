<?php
$isEdit = !empty($product);
$priceJson = $isEdit ? (string)$product['price_json'] : "{\n  \"month\": 10,\n  \"quarter\": 28,\n  \"year\": 100\n}";
?>
<div class="mis-card">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="fas fa-box text-primary me-2"></i><?= $isEdit ? '编辑产品' : '新建产品' ?></h2>
    <a class="btn btn-secondary" href="<?= e(url_with_action('admin.php', 'products')) ?>">返回列表</a>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm">
      <?php foreach ($errors as $err): ?>
        <div><i class="fas fa-exclamation-circle me-1"></i><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="needs-validation">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="mb-3">
      <label class="form-label fw-bold">产品名称</label>
      <input class="form-control" name="name" value="<?= e($isEdit ? $product['name'] : '') ?>" required placeholder="例如：入门级云服务器 2G">
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-bold">交付方式</label>
        <select class="form-select" name="delivery_mode">
          <?php $dm = $isEdit ? $product['delivery_mode'] : 'manual'; ?>
          <option value="manual" <?= $dm === 'manual' ? 'selected' : '' ?>>人工交付 (Manual)</option>
          <option value="provider_api" <?= $dm === 'provider_api' ? 'selected' : '' ?>>API 自动对接</option>
        </select>
        <div class="form-text">选择服务开通的处理方式</div>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">计费模式</label>
        <select class="form-select" name="billing_mode">
          <?php $bm = $isEdit ? $product['billing_mode'] : 'periodic'; ?>
          <option value="periodic" <?= $bm === 'periodic' ? 'selected' : '' ?>>周期计费 (包月/年)</option>
          <option value="metered" <?= $bm === 'metered' ? 'selected' : '' ?>>按量计费 (按秒)</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">产品状态</label>
        <?php $st = $isEdit ? $product['status'] : 'active'; ?>
        <select class="form-select" name="status">
          <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>🟢 正常销售</option>
          <option value="hidden" <?= $st === 'hidden' ? 'selected' : '' ?>>🔒 隐藏 (仅链接购买)</option>
          <option value="disabled" <?= $st === 'disabled' ? 'selected' : '' ?>>🔴 下架/停售</option>
        </select>
      </div>
    </div>

    <div class="mt-4">
      <label class="form-label fw-bold">定价配置 (JSON)</label>
      <div class="bg-light p-2 rounded border">
        <textarea class="form-control font-monospace border-0 bg-light" name="price_json" rows="8"><?= e($priceJson) ?></textarea>
      </div>
      <div class="form-text text-muted mt-2">
        <i class="fas fa-info-circle"></i> 周期示例: <code>{"month": 10, "year": 100}</code> | 按量示例: <code>{"per_second": 0.00002}</code>
      </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
      <button class="btn btn-primary px-4" type="submit">
        <i class="fas fa-save me-2"></i>保存更改
      </button>
    </div>
  </form>
</div>
