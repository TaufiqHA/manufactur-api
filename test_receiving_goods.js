const ReceivingGoods = require('./models/ReceivingGoods');
const Material = require('./models/Material');
const PurchaseOrder = require('./models/PurchaseOrder');

async function testReceivingGoodsStockUpdate() {
  console.log('Testing Receiving Goods Stock Update Functionality...');

  try {
    // Create a test material
    const testMaterial = await Material.create({
      id: 'test_mat_001',
      code: 'TEST-001',
      name: 'Test Material',
      unit: 'Pcs',
      currentStock: 100,
      safetyStock: 10,
      pricePerUnit: 10,
      category: 'RAW'
    });
    
    console.log('Created test material:', testMaterial.name);

    // Create a test purchase order with items
    const testPO = await PurchaseOrder.create({
      id: 'test_po_001',
      code: 'PO-TEST-001',
      date: new Date().toISOString().split('T')[0],
      supplierId: 'sup1',
      description: 'Test PO for stock update',
      items: [
        {
          materialId: 'test_mat_001',
          materialName: 'Test Material',
          materialCode: 'TEST-001',
          qty: 50,
          unitPrice: 10,
          totalPrice: 500
        }
      ],
      status: 'OPEN',
      grandTotal: 500
    });
    
    console.log('Created test PO:', testPO.code);

    // Verify initial stock
    const initialMaterial = await Material.findById('test_mat_001');
    console.log('Initial stock:', initialMaterial.currentStock);

    // Create receiving goods that should update the stock
    const receivingGoods = await ReceivingGoods.create({
      id: 'test_rg_001',
      code: 'RG-TEST-001',
      date: new Date().toISOString().split('T')[0],
      poId: 'test_po_001',
      items: [
        {
          materialId: 'test_mat_001',
          materialName: 'Test Material',
          materialCode: 'TEST-001',
          qty: 30,  // This should be added to the stock
          receivedQty: 30  // This takes precedence if available
        }
      ]
    });
    
    console.log('Created receiving goods:', receivingGoods.code);

    // Check updated stock
    const updatedMaterial = await Material.findById('test_mat_001');
    console.log('Updated stock:', updatedMaterial.currentStock);
    
    // Expected: 100 (initial) + 30 (received) = 130
    if (updatedMaterial.currentStock === 130) {
      console.log('✅ Stock update test PASSED!');
    } else {
      console.log('❌ Stock update test FAILED! Expected: 130, Got:', updatedMaterial.currentStock);
    }

    // Cleanup - delete test data
    await ReceivingGoods.delete('test_rg_001');
    await PurchaseOrder.delete('test_po_001');
    await Material.delete('test_mat_001');
    
    console.log('Cleanup completed.');
  } catch (error) {
    console.error('Error during test:', error);
  }
}

// Run the test
testReceivingGoodsStockUpdate();
