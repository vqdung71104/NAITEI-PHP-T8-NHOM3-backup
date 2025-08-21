import { useState} from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import { Head, useForm, router } from '@inertiajs/react';
import { Card, CardContent } from "@/components/ui/card";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid, Legend, PieChart } from 'recharts';
import axios from 'axios';






const breadcrumbs = [
  {
    title: 'Admin Dashboard',
    href: '/admin/dashboard',
  },
];

export default function AdminDashboard({
  categories = [],
  products = [],
  orders = [],
  pagination = {}
}) {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [currentPage, setCurrentPage] = useState(pagination.current_page || 1);
  const [editingCategory, setEditingCategory] = useState(null);
  const [editingProduct, setEditingProduct] = useState(null);
  const itemsPerPage = pagination.per_page || 10;
  const [filters, setFilters] = useState({ 
    status: '', 
    startDate: '', 
    endDate: '' 
  });
  const [selectedStatus, setSelectedStatus] = useState({});
  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'];
  const [statistics, setStatistics] = useState({});
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  // Forms
  const categoryForm = useForm({
    name: '',
    description: ''
  });

  const productForm = useForm({
    name: '',
    description: '',
    price: '',
    stock: '',
    category_id: '',
    image_url: '',
    author: '',
  });

  const orderForm = useForm({
    status: ''
  });

  const paginatedProducts = products.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  // Category handlers
  const handleCategorySubmit = (e) => {
    e.preventDefault();
    if (editingCategory) {
      categoryForm.put(route('admin.categories.update', editingCategory.id), {
        onSuccess: () => {
          setEditingCategory(null);
          categoryForm.reset();
        }
      });
    } else {
      categoryForm.post(route('admin.categories.store'), {
        onSuccess: () => {
          categoryForm.reset();
        }
      });
    }
  };

  const handleEditCategory = (category) => {
    setEditingCategory(category);
    categoryForm.setData({
      name: category.name,
      description: category.description || ''
    });
  };

  const handleDeleteCategory = (categoryId) => {
    if (confirm('Are you sure you want to delete this category?')) {
      categoryForm.delete(route('admin.categories.destroy', categoryId));
    }
  };

  // Product handlers
  const handleProductSubmit = (e) => {
    e.preventDefault();
    if (editingProduct) {
      productForm.put(route('admin.products.update', editingProduct.id), {
        onSuccess: () => {
          setEditingProduct(null);
          productForm.reset();
        }
      });
    } else {
      productForm.post(route('admin.products.store'), {
        onSuccess: () => {
          productForm.reset();
        }
      });
    }
  };

  const handleEditProduct = (product) => {
    setEditingProduct(product);
    productForm.setData({
      name: product.name,
      description: product.description || '',
      price: product.price,
      stock: product.stock,
      category_id: product.category_id,
      image_url: product.image_url || '',
      author: product.author || ''
    });
  };

  const handleDeleteProduct = (productId) => {
    if (confirm('Are you sure you want to delete this product?')) {
      productForm.delete(route('admin.products.destroy', productId));
    }
  };

  // Order handlers
  const [filteredOrders, setFilteredOrders] = useState(orders);
  const [editingOrder, setEditingOrder] = useState(null);
  
  // Hàm xử lý nút Find
  const handleFindOrders = () => {
    let result = orders;
  
    if (filters.status) {
      result = result.filter(
        (order) => order.status.toLowerCase() === filters.status.toLowerCase()
      );
    }
  
    if (filters.startDate && filters.endDate) {
      const startDate = new Date(filters.startDate);
      const endDate = new Date(filters.endDate);
      endDate.setHours(23, 59, 59, 999); // Đặt thời gian về cuối ngày
      
      result = result.filter((order) => {
        const orderDate = new Date(order.created_at);
        return orderDate >= startDate && orderDate <= endDate;
      });
    }
    
    setFilteredOrders(result);
  };
  
  // Hàm format lại ngày giờ
  const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    return date.toLocaleString(); 
  };

  // Hàm xử lý khi ngày bắt đầu thay đổi
  const handleStartDateChange = (date) => {
    setFilters(prev => ({ 
      ...prev, 
      startDate: date,
      // Nếu ngày kết thúc nhỏ hơn ngày bắt đầu mới, cập nhật ngày kết thúc
      endDate: prev.endDate && new Date(prev.endDate) < new Date(date) ? date : prev.endDate
    }));
  };

  // Hàm bắt đầu chỉnh sửa trạng thái
  const handleStartEditOrder = (order) => {
    setEditingOrder(order.id);
    setSelectedStatus(prev => ({
      ...prev,
      [order.id]: order.status
    }));

    // Chỉ cần set status vào form để chuẩn bị update
    orderForm.setData({
      status: order.status
    });
  };

  // Khi thay đổi status trong select
  const handleStatusChange = (orderId, newStatus) => {
    setSelectedStatus(prev => ({
      ...prev,
      [orderId]: newStatus
    }));

    // Cập nhật vào form data
    orderForm.setData('status', newStatus);
  };

  // Lưu trạng thái mới
  const handleSaveStatus = (orderId) => {
    const newStatus = selectedStatus[orderId];

    if (!newStatus) {
      alert('Please select a status');
      return;
    }

    // Chỉ gửi status khi PUT
    orderForm.put(route('admin.orders.update', orderId), {
      data: { status: newStatus }, // ép chỉ gửi status
      onSuccess: () => {
        setEditingOrder(null);
        const updatedOrders = orders.map(order =>
          order.id === orderId ? { ...order, status: newStatus } : order
        );
        setFilteredOrders(updatedOrders);
      },
      onError: (errors) => {
        console.error('Error updating order status:', errors);
        alert('Failed to update order status');
      }
    });
  };

  // Hàm hủy chỉnh sửa
  const handleCancelEdit = (orderId) => {
    setEditingOrder(null);
    setSelectedStatus(prev => {
      const newState = { ...prev };
      delete newState[orderId];
      return newState;
    });
  };

  return (
    <AdminLayout breadcrumbs={breadcrumbs}>
      <Head title="Admin Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
        {/* Tab Navigation */}
        <div className="flex border-b border-gray-200 dark:border-gray-700">
          {['dashboard', 'categories', 'products', 'orders'].map((tab) => (
            <button
              key={tab}
              className={`px-4 py-2 font-medium text-sm ${
                activeTab === tab
                  ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
              }`}
              onClick={() => setActiveTab(tab)}
            >
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </button>
          ))}
        </div>

        {/* Dashboard Tab */}
        
        {activeTab === 'dashboard' && (
          <div className="grid gap-4 md:grid-cols-2">
            
            <div className="p-4">
              <h2 className="text-2xl font-bold mb-4">Dashboard Overview</h2>
          
              {loading ? (
                <p>Loading statistics...</p>
              ) : error ? (
                <p className="text-red-500">{error}</p>
              ) : (
                <>
                  {/* Cards thống kê */}
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div className="bg-white shadow-md rounded-lg p-6">
                      <h3 className="text-lg font-semibold mb-2">Tổng số đơn hàng</h3>
                      <p className="text-3xl font-bold">{statistics.total_orders}</p>
                    </div>
              
                    <div className="bg-white shadow-md rounded-lg p-6">
                      <h3 className="text-lg font-semibold mb-2">Tổng doanh thu</h3>
                      <p className="text-3xl font-bold">
                        {statistics.total_revenue?.toLocaleString()} ₫
                      </p>
                    </div>
              
                    <div className="bg-white shadow-md rounded-lg p-6">
                      <h3 className="text-lg font-semibold mb-2">Đơn đang xử lý</h3>
                      <p className="text-3xl font-bold">{statistics.processing_orders}</p>
                    </div>
              
                    <div className="bg-white shadow-md rounded-lg p-6">
                      <h3 className="text-lg font-semibold mb-2">Đơn đã hủy</h3>
                      <p className="text-3xl font-bold">{statistics.cancelled_orders}</p>
                    </div>
              
                    <div className="bg-white shadow-md rounded-lg p-6">
                      <h3 className="text-lg font-semibold mb-2">Đơn đã hoàn thành</h3>
                      <p className="text-3xl font-bold">{statistics.completed_orders}</p>
                    </div>
                  </div>
              
                  {/* Biểu đồ */}
                  <div className="bg-white shadow-md rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Thống kê đơn hàng theo trạng thái</h3>
                    <ResponsiveContainer width="100%" height={300}>
                      <BarChart data={[
                        { name: "Processing", value: statistics.processing_orders || 0 },
                        { name: "Completed", value: statistics.completed_orders || 0 },
                        { name: "Cancelled", value: statistics.cancelled_orders || 0 }
                      ]}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="name" />
                        <YAxis allowDecimals={false} />
                        <Tooltip />
                        <Legend />
                        <Bar dataKey="value" fill="#4F46E5" name="Số lượng" />
                      </BarChart>
                    </ResponsiveContainer>
                  </div>
                </>
              )}
            </div>
        
          </div>
        )}

        {/* Categories Tab */}
        {activeTab === 'categories' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">Manage Categories</h3>
              
              {/* Category Form */}
              <form onSubmit={handleCategorySubmit} className="flex gap-2 mb-4">
                <input
                  type="text"
                  className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  placeholder="Category name"
                  value={categoryForm.data.name}
                  onChange={(e) => categoryForm.setData('name', e.target.value)}
                />
                <input
                  type="text"
                  className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  placeholder="Description (optional)"
                  value={categoryForm.data.description}
                  onChange={(e) => categoryForm.setData('description', e.target.value)}
                />
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  disabled={categoryForm.processing}
                >
                  {editingCategory ? 'Update Category' : 'Add Category'}
                </button>
                {editingCategory && (
                  <button
                    type="button"
                    onClick={() => {
                      setEditingCategory(null);
                      categoryForm.reset();
                    }}
                    className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                  >
                    Cancel
                  </button>
                )}
              </form>

              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {categories.map((category) => (
                      <tr key={category.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.description || '-'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm">
                          <button
                            onClick={() => handleEditCategory(category)}
                            className="mr-2 px-3 py-1 bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-white rounded-md hover:bg-blue-300 dark:hover:bg-blue-500"
                          >
                            Edit
                          </button>
                          <button
                            onClick={() => handleDeleteCategory(category.id)}
                            className="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                          >
                            Delete
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* Products Tab */}
        {activeTab === 'products' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">Manage Products</h3>

              {/* Product Form */}
              <form onSubmit={handleProductSubmit} className="grid gap-4 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input 
                    type="text" 
                    placeholder="Product name" 
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.name}
                    onChange={(e) => productForm.setData('name', e.target.value)}
                  />
                  <input 
                    type="number" 
                    placeholder="Price" 
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.price}
                    onChange={(e) => productForm.setData('price', e.target.value)}
                  />
                  <input 
                  type="text" 
                  placeholder="Author" 
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.author}
                  onChange={(e) => productForm.setData('author', e.target.value)}
                  required
                />
                </div>

                <textarea 
                  placeholder="Description" 
                  rows={3} 
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.description}
                  onChange={(e) => productForm.setData('description', e.target.value)}
                />

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input 
                    type="number" 
                    placeholder="Stock quantity" 
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.stock}
                    onChange={(e) => productForm.setData('stock', e.target.value)}
                  />
                  <select 
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.category_id}
                    onChange={(e) => productForm.setData('category_id', e.target.value)}
                  >
                    <option value="">Select category</option>
                    {categories.map(category => (
                      <option key={category.id} value={category.id}>{category.name}</option>
                    ))}
                  </select>
                  
                </div>

                <input
                  type="text"
                  placeholder="Image URL (optional)"
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.image_url}
                  onChange={(e) => productForm.setData('image_url', e.target.value)}
                />


                <div className="flex gap-2">
                  <button 
                    type="submit"
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                    disabled={productForm.processing}
                  >
                    {editingProduct ? 'Update Product' : 'Add Product'}
                  </button>
                  {editingProduct && (
                    <button
                      type="button"
                      onClick={() => {
                        setEditingProduct(null);
                        productForm.reset();
                      }}
                      className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                      Cancel
                    </button>
                  )}
                </div>
              </form>

              {/* Products Table */}
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Author</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {paginatedProducts.map((product) => (
                      <tr key={product.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.author || 'N/A'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{Number(product.price).toLocaleString("vi-VN")} VND</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.stock}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.category?.name || 'N/A'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          {product.image_url ? (
                            <img src={product.image_url} alt={product.name} className="h-10 w-10 object-cover rounded" />
                          ) : 'No image'}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          <button 
                            onClick={() => handleEditProduct(product)}
                            className="mr-2 px-3 py-1 bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-white rounded-md hover:bg-blue-300 dark:hover:bg-blue-500"
                          >
                            Edit
                          </button>
                          <button 
                            onClick={() => handleDeleteProduct(product.id)}
                            className="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                          >
                            Delete
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              <div className="flex justify-between items-center mt-4">
                <button 
                  disabled={currentPage === 1} 
                  onClick={() => setCurrentPage(currentPage - 1)}
                  className="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md disabled:opacity-50"
                >
                  Previous
                </button>
                <span className="text-gray-700 dark:text-gray-300">Page {currentPage} of {Math.ceil(products.length / itemsPerPage)}</span>
                <button 
                  disabled={currentPage === Math.ceil(products.length / itemsPerPage)} 
                  onClick={() => setCurrentPage(currentPage + 1)}
                  className="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md disabled:opacity-50"
                >
                  Next
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Orders Tab */}
        {/* Orders Tab */}
        {activeTab === 'orders' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">Manage Orders</h3>
              
              {/* Bộ lọc */}
              <div className="flex flex-col md:flex-row gap-4 mb-4">
                <select
                  className="w-full md:w-48 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                  value={filters.status}
                  onChange={(e) =>
                    setFilters((prev) => ({ ...prev, status: e.target.value }))
                  }
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="return">Return</option>
                </select>
                
                <div className="flex items-center gap-2">
                  <input
                    type="date"
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                    value={filters.startDate}
                    onChange={(e) => handleStartDateChange(e.target.value)}
                    max={filters.endDate || undefined}
                  />
                  <span className="text-gray-500 dark:text-gray-400">to</span>
                  <input
                    type="date"
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                    value={filters.endDate}
                    onChange={(e) => setFilters(prev => ({ ...prev, endDate: e.target.value }))}
                    min={filters.startDate || undefined}
                  />
                </div>
                
                <button
                  onClick={() => setFilters({ status: '', startDate: '', endDate: '' })}
                  className="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                >
                  Clear Filters
                </button>
                <button
                  onClick={handleFindOrders}
                  className="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  Find
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order ID</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order date</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {filteredOrders.map((order) => (
                      <tr key={order.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{order.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{order.user?.name || 'Guest'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{Number(order.total_price).toLocaleString("vi-VN")} VND</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{formatDateTime(order.created_at)}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          {editingOrder === order.id ? (
                            <select
                              className="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                              value={selectedStatus[order.id] || order.status}
                              onChange={(e) => handleStatusChange(order.id, e.target.value)}
                            >
                              <option value="pending">Pending</option>
                              <option value="processing">Processing</option>
                              <option value="completed">Completed</option>
                              <option value="cancelled">Cancelled</option>
                              <option value="return">Return</option>
                            </select>
                          ) : (
                            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                              order.status === 'completed' ? 'bg-green-100 text-green-800' :
                              order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                              order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                              order.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                              'bg-purple-100 text-purple-800'
                            }`}>
                              {order.status}
                            </span>
                          )}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          {editingOrder === order.id ? (
                            <div className="flex gap-2">
                              <button
                                onClick={() => handleSaveStatus(order.id)}
                                className="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700"
                                disabled={orderForm.processing}
                              >
                                {orderForm.processing ? 'Saving...' : 'Save'}
                              </button>
                              <button
                                onClick={() => handleCancelEdit(order.id)}
                                className="px-3 py-1 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                              >
                                Cancel
                              </button>
                            </div>
                          ) : (
                            <button
                              onClick={() => handleStartEditOrder(order)}
                              className="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            >
                              Change Status
                            </button>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}