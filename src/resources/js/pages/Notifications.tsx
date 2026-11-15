import { useState, useEffect } from 'react';
import api from '../api/axios';
import { Link } from 'react-router-dom';
import { NotificationItem } from '../types/index';

const parseData = (data: any) => {
    if (!data) return {};
    if (typeof data === 'string') {
        try { return JSON.parse(data); } catch { return {}; }
    }
    return data;
};

export default function Notifications() {
    const [notifications, setNotifications] = useState<NotificationItem[]>([]);
    const [filter, setFilter] = useState<'all' | 'unread' | 'read'>('all');
    const [loading, setLoading] = useState(true);

    const fetchNotifications = async () => {
        setLoading(true);
        try {
            const res = await api.get(`/notifications?filter=${filter === 'all' ? '' : filter}`);
            const items = res.data?.data?.data || res.data?.data || [];
            setNotifications(Array.isArray(items) ? items : []);
        } catch (err) {
            console.error("Error:", err);
            setNotifications([]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchNotifications();
    }, [filter]);

    const handleMarkAsRead = async (id: string) => {
        await api.post(`/notifications/${id}/read`);
        fetchNotifications();
    };

    const handleMarkAllRead = async () => {
        await api.post('/notifications/read-all');
        fetchNotifications();
    };

    const handleDelete = async (id: string) => {
        if (window.confirm('آیا از حذف این اعلان مطمئن هستید؟')) {
            await api.delete(`/notifications/${id}`);
            fetchNotifications();
        }
    };

    return (
        <div className="max-w-4xl mx-auto">
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">اعلان ها</h1>
                <button onClick={handleMarkAllRead} className="text-sm text-blue-600 hover:underline">
                    علامت گذاری همه به عنوان خوانده شده
                </button>
            </div>

            <div className="flex gap-2 mb-4">
                {(['all', 'unread', 'read'] as const).map((f) => (
                    <button
                        key={f}
                        onClick={() => setFilter(f)}
                        className={`px-4 py-1 rounded-full text-sm cursor-pointer ${filter === f ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'}`}
                    >
                        {f === 'all' ? 'همه' : f === 'unread' ? 'خوانده نشده' : 'خوانده شده'}
                    </button>
                ))}
            </div>

            {loading ? (
                <p className="text-center py-8">در حال بارگذاری...</p>
            ) : notifications.length === 0 ? (
                <div className="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                    اعلانی وجود ندارد.
                </div>
            ) : (
                <div className="space-y-3">
                    {notifications.map((n) => {
                        const data = parseData(n.data);
                        return (
                            <div
                                key={n.id}
                                className={`bg-white p-4 rounded-lg shadow-sm border-l-4 ${n.read_at ? 'border-gray-300' : 'border-blue-500'} flex justify-between items-start`}
                            >
                                <div>
                                    <p className={`font-semibold ${n.read_at ? 'text-gray-600' : 'text-gray-900'}`}>
                                        {data.title || 'اعلان'}
                                    </p>
                                    <p className="text-sm text-gray-500 mt-1">
                                        {data.message || ''}
                                    </p>
                                    <p className="text-xs text-gray-400 mt-2">
                                        {new Date(n.created_at).toLocaleString('fa-IR')}
                                    </p>
                                    {data.url && (
                                        <Link to={data.url} className="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                            مشاهده جزئیات
                                        </Link>
                                    )}
                                </div>
                                <div className="flex gap-2 flex-shrink-0">
                                    {!n.read_at && (
                                        <button onClick={() => handleMarkAsRead(n.id)} className="text-xs text-blue-600 hover:underline">خوانده شد</button>
                                    )}
                                    <button onClick={() => handleDelete(n.id)} className="text-xs text-red-600 hover:underline">حذف</button>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}