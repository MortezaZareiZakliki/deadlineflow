import { Link, Outlet, useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import { useState, useEffect } from "react";
import Footer from "./Footer";
import api from "../api/axios";
import { toPersianNumber } from "../utils/number";

export default function Layout() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
    const [unreadCount, setUnreadCount] = useState(0);

    const handleLogout = async () => {
        await logout();
        navigate("/login", { replace: true });
    };

    const menuItems = [
        { path: "/dashboard", label: "داشبورد" },
        { path: "/projects", label: "پروژه ها" },
        { path: "/tasks", label: "تسک ها" },
    ];

    const fetchUnreadCount = () => {
        api.get('/notifications?filter=unread')
            .then(res => {
                setUnreadCount(res.data.data?.total || 0);
            })
            .catch(err => console.error(err));
    };

    useEffect(() => {
        fetchUnreadCount();
        const interval = setInterval(fetchUnreadCount, 60000);
        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        setIsMobileMenuOpen(false);
    }, [location.pathname]);

    return (
        <div className="min-h-screen bg-gray-100 flex flex-col">
            <nav className="bg-white shadow relative">
                <div className="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
                    <Link
                        to="/dashboard"
                        className="text-xl font-bold text-blue-600 flex items-center gap-2"
                    >
                        ⏳ DeadlineFlow
                    </Link>

                    <div className="hidden md:flex items-center gap-6">
                        {menuItems.map((item) => (
                            <Link
                                key={item.path}
                                to={item.path}
                                className={`hover:text-blue-600 ${location.pathname === item.path ? "text-blue-600 font-bold" : "text-gray-700"}`}
                            >
                                {item.label}
                            </Link>
                        ))}
                        <div className="border-r border-gray-300 h-6 mx-2"></div>

                        <Link to="/profile" className={`hover:text-blue-600 ${location.pathname === "/profile" ? "text-blue-600 font-bold text-sm" : "text-gray-500 text-sm"}`}>
                            {toPersianNumber(user?.name)}
                        </Link>

                        <Link
                            to="/notifications"
                            className={`relative transition-colors flex items-center ${location.pathname === "/notifications"
                                ? "text-blue-600 font-bold"
                                : "text-gray-700 hover:text-blue-600"
                                }`}
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                strokeWidth={1.5}
                                stroke="currentColor"
                                className="w-6 h-6"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                                />
                            </svg>
                            {unreadCount > 0 && (
                                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-[16px] h-4 flex items-center justify-center font-bold px-1">
                                    {toPersianNumber(unreadCount > 99 ? '99+' : unreadCount)}
                                </span>
                            )}
                        </Link>

                        <button
                            onClick={handleLogout}
                            className="text-red-600 hover:text-red-800 text-sm"
                        >
                            خروج
                        </button>
                    </div>
                    <button
                        className="md:hidden text-gray-600 text-2xl"
                        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                    >
                        ☰
                    </button>
                </div>

                {isMobileMenuOpen && (
                    <div className="md:hidden absolute top-full left-0 right-0 bg-white shadow-lg border-t z-50">
                        <div className="flex flex-col p-4 space-y-4">
                            {menuItems.map((item) => (
                                <Link
                                    key={item.path}
                                    to={item.path}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className={`block ${location.pathname === item.path ? "text-blue-600 font-bold" : "text-gray-700"}`}
                                >
                                    {item.label}
                                </Link>
                            ))}
                            <div className="border-t border-gray-200 pt-4 flex justify-between items-center">
                                <Link
                                    to="/profile"
                                    className={`hover:text-blue-600 ${location.pathname === "/profile" ? "text-blue-600 font-bold text-sm" : "text-gray-500 text-sm"}`}
                                >
                                    {toPersianNumber(user?.name)}
                                </Link>

                                <Link
                                    to="/notifications"
                                    className={`relative transition-colors flex items-center ${location.pathname === "/notifications"
                                        ? "text-blue-600 font-bold"
                                        : "text-gray-700 hover:text-blue-600"
                                        }`}
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth={1.5}
                                        stroke="currentColor"
                                        className="w-6 h-6"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                                        />
                                    </svg>
                                    {unreadCount > 0 && (
                                        <span className="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-[16px] h-4 flex items-center justify-center font-bold px-1">
                                            {toPersianNumber(unreadCount > 99 ? '99+' : unreadCount)}
                                        </span>
                                    )}
                                </Link>

                                <button
                                    onClick={handleLogout}
                                    className="text-red-600 text-sm"
                                >
                                    خروج
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </nav>

            <main className="flex-1 max-w-6xl mx-auto w-full p-4 md:p-6">
                <Outlet />
            </main>

            <Footer />
        </div>
    );
}
