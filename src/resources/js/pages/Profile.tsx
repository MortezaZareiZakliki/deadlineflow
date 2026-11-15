import { useState, useEffect, SyntheticEvent, useRef } from "react";
import api from "../api/axios";
import { useAuth } from "../context/AuthContext";
import { Session } from "../types/index";
import ErrorList from '../components/ErrorList';

export default function Profile() {
    const { user, updateUser } = useAuth();
    const hasFetched = useRef(false);

    const [name, setName] = useState(user?.name || "");
    const [infoMessage, setInfoMessage] = useState("");
    const [infoError, setInfoError] = useState("");

    const [currentPassword, setCurrentPassword] = useState("");
    const [newPassword, setNewPassword] = useState("");
    const [passwordConfirmation, setPasswordConfirmation] = useState("");
    const [passMessage, setPassMessage] = useState("");
    const [passError, setPassError] = useState("");

    const [sessions, setSessions] = useState<Session[]>([]);

    const fetchSessions = () => {
        api.get("/sessions")
            .then((res) => setSessions(res.data.data))
            .catch((err) => console.error(err));
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchSessions();
    }, []);

    useEffect(() => {
        if (user?.name) {
            setName(user.name);
        }
    }, [user]);

    const handleUpdateInfo = async (e: SyntheticEvent) => {
        e.preventDefault();
        setInfoMessage("");
        setInfoError("");
        try {
            const response = await api.put("/profile/info", { name });
            updateUser(response.data.data);
            setInfoMessage("اطلاعات شخصی با موفقیت آپدیت شد.");
        } catch (err: any) {
            setInfoError(err.response?.data?.errors || err.response?.data?.message || 'خطا در آپدیت اطلاعات');
        }
    };

    const handleUpdatePassword = async (e: SyntheticEvent) => {
        e.preventDefault();
        setPassMessage("");
        setPassError("");
        try {
            await api.put("/profile/password", {
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: passwordConfirmation,
            });
            setPassMessage("رمز عبور با موفقیت تغییر کرد.");
            setCurrentPassword("");
            setNewPassword("");
            setPasswordConfirmation("");
        } catch (err: any) {
            setPassError(err.response?.data?.errors || err.response?.data?.message || 'خطا در تغییر رمز عبور');
        }
    };

    const handleDeleteSession = async (id: number) => {
        if (
            window.confirm(
                "آیا این نشست حذف شود؟ (از آن دستگاه خارج خواهید شد)",
            )
        ) {
            await api.delete(`/sessions/${id}`);
            fetchSessions();
        }
    };

    return (
        <div className="max-w-4xl mx-auto space-y-6">
            <h1 className="text-2xl font-bold text-gray-800">پروفایل کاربری</h1>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 className="text-lg font-bold mb-4">اطلاعات شخصی</h2>
                    {infoMessage && (
                        <div className="bg-green-100 text-green-700 p-2 rounded mb-4 text-sm">
                            {infoMessage}
                        </div>
                    )}
                    {infoError && <ErrorList errors={infoError} />}
                    <form onSubmit={handleUpdateInfo} className="space-y-4">
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                نام نمایشی
                            </label>
                            <input
                                type="text"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                className="w-full border p-2 rounded"
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full"
                        >
                            ذخیره نام
                        </button>
                    </form>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 className="text-lg font-bold mb-4">تغییر رمز عبور</h2>
                    {passMessage && (
                        <div className="bg-green-100 text-green-700 p-2 rounded mb-4 text-sm">
                            {passMessage}
                        </div>
                    )}
                    {passError && <ErrorList errors={passError} />}
                    <form onSubmit={handleUpdatePassword} className="space-y-4">
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                رمز عبور فعلی
                            </label>
                            <input
                                type="password"
                                value={currentPassword}
                                onChange={(e) =>
                                    setCurrentPassword(e.target.value)
                                }
                                className="w-full border p-2 rounded"
                                required
                            />
                        </div>
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                رمز عبور جدید
                            </label>
                            <input
                                type="password"
                                value={newPassword}
                                onChange={(e) => setNewPassword(e.target.value)}
                                className="w-full border p-2 rounded"
                                required
                            />
                        </div>
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                تکرار رمز عبور جدید
                            </label>
                            <input
                                type="password"
                                value={passwordConfirmation}
                                onChange={(e) =>
                                    setPasswordConfirmation(e.target.value)
                                }
                                className="w-full border p-2 rounded"
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full"
                        >
                            تغییر رمز عبور
                        </button>
                    </form>
                </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-bold">
                        دستگاه های متصل (نشست ها)
                    </h2>
                    <button
                        onClick={fetchSessions}
                        className="text-sm text-blue-600 hover:underline"
                    >
                        بروزرسانی لیست
                    </button>
                </div>
                <div className="space-y-3">
                    {sessions.length === 0 ? (
                        <p className="text-gray-500 text-sm">
                            هیچ نشست فعالی یافت نشد.
                        </p>
                    ) : (
                        sessions.map((session) => (
                            <div
                                key={session.id}
                                className="flex flex-col md:flex-row justify-between md:items-center p-4 bg-gray-50 rounded-lg border border-gray-200 gap-4"
                            >
                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <span className="text-lg">
                                            {session.device_type === "موبایل"
                                                ? "📱"
                                                : session.device_type === "تبلت"
                                                    ? "📱"
                                                    : "💻"}
                                        </span>
                                        <p className="font-semibold text-sm text-gray-800">
                                            {session.browser} در{" "}
                                            {session.platform}
                                        </p>
                                        {session.is_current && (
                                            <span className="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">
                                                دستگاه فعلی
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-xs text-gray-500 mt-1">
                                        IP:{" "}
                                        <span className="font-mono">
                                            {session.ip_address || "نامشخص"}
                                        </span>
                                    </p>
                                    <p className="text-xs text-gray-400 mt-1">
                                        آخرین فعالیت: {session.last_used_at}
                                    </p>
                                </div>
                                {!session.is_current && (
                                    <button
                                        onClick={() =>
                                            handleDeleteSession(session.id)
                                        }
                                        className="text-red-600 text-sm border border-red-200 px-3 py-1 rounded hover:bg-red-50 whitespace-nowrap"
                                    >
                                        خروج از این دستگاه
                                    </button>
                                )}
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
