import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';

export default function AdminLayout({ children, breadcrumbs, ...props }) {
  return (
    <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
        {/* Admin Header */}
        <div className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center py-4">
              <div className="flex items-center">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                  Admin Dashboard
                </h1>
              </div>
              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-500 dark:text-gray-400">
                  Welcome, Admin
                </span>
                <a
                  href={route('home')}
                  className="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View Site
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
          {children}
        </main>
      </div>
    </AppLayoutTemplate>
  );
}



