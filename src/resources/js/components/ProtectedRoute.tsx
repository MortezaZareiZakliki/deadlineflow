import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function ProtectedRoute() {
  const { user, isLoading } = useAuth();

  if (isLoading) return <div className="text-center py-8">در حال بارگذاری...</div>;
  if (!user) return <Navigate to="/login" replace />;
  return <Outlet />;
}