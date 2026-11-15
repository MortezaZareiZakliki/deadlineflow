import { useEffect, useState, useRef } from "react";
import { useAuth } from "../context/AuthContext";
import api from "../api/axios";
import { PieChart, Pie, ResponsiveContainer, Tooltip } from 'recharts';
import { Link } from "react-router-dom";
import { formatToJalali } from "../utils/date";
import { toPersianNumber } from '../utils/number';
import { Stats, UpcomingTask, ProjectSummary } from '../types/index';

const COLORS = ["#3b82f6", "#f59e0b", "#10b981"];

export default function Dashboard() {
    const { user } = useAuth();
    const [stats, setStats] = useState<Stats | null>(null);
    const [loading, setLoading] = useState(true);
    const hasFetched = useRef(false);
    const [upcomingTasks, setUpcomingTasks] = useState<UpcomingTask[]>([]);
    const [projects, setProjects] = useState<ProjectSummary[]>([]);

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        Promise.all([
            api.get("/tasks/stats"),
            api.get("/tasks/upcoming"),
            api.get("/projects"),
        ])
            .then(([statsRes, upcomingRes, projectsRes]) => {
                setStats(statsRes.data.data);
                setUpcomingTasks(upcomingRes.data.data);
                setProjects(projectsRes.data.data);
            })
            .catch((err) => console.error("Error fetching :", err))
            .finally(() => setLoading(false));
    }, []);

    const chartData = ([
        { name: 'در انتظار', value: stats?.todo || 0, fill: COLORS[0] },
        { name: 'در حال انجام', value: stats?.in_progress || 0, fill: COLORS[1] },
        { name: 'انجام شده', value: stats?.done || 0, fill: COLORS[2] },
    ]);

    return (
        <div className="space-y-6">
            <h1 className="text-3xl font-bold text-gray-800">
                سلام، {user?.name} 👋
            </h1>

            <div className="bg-white p-4 rounded-xl shadow-sm">
                <h2 className="text-sm font-bold text-gray-700 mb-3">
                    پروژه های شما
                </h2>
                <div className="flex flex-wrap gap-3">
                    {loading ? (
                        <span className="text-gray-400 text-sm">
                            در حال بارگذاری...
                        </span>
                    ) : projects.length === 0 ? (
                        <span className="text-gray-400 text-sm">
                            شما هنوز پروژه ای نساخته اید.
                        </span>
                    ) : (
                        projects.map((project) => (
                            <div
                                key={project.id}
                                className="flex items-center gap-2 bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg"
                            >
                                <span className="font-semibold text-gray-800 text-sm">
                                    {toPersianNumber(project.name)}
                                </span>
                                <span className="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-bold">
                                    {toPersianNumber(project.tasks_count || 0)} تسک
                                </span>
                            </div>
                        ))
                    )}
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border-t-4 border-blue-500">
                    <p className="text-gray-500 text-sm mb-2">
                        تسک های در انتظار (To Do)
                    </p>
                    <p className="text-3xl font-bold text-blue-600">
                        {loading ? "..." : toPersianNumber(stats?.todo || 0)}
                    </p>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm border-t-4 border-yellow-500">
                    <p className="text-gray-500 text-sm mb-2">در حال انجام</p>
                    <p className="text-3xl font-bold text-yellow-600">
                        {loading ? "..." : toPersianNumber(stats?.in_progress || 0)}
                    </p>
                </div>
                <div className="bg-white p-6 rounded-xl shadow-sm border-t-4 border-green-500">
                    <p className="text-gray-500 text-sm mb-2">انجام شده</p>
                    <p className="text-3xl font-bold text-green-600">
                        {loading ? "..." : toPersianNumber(stats?.done || 0)}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm">
                    <h2 className="text-lg font-bold text-gray-800 mb-4">
                        وضعیت کلی تسک ها
                    </h2>
                    <div className="h-64">
                        {!loading && stats?.total ? (
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={chartData}
                                        cx="50%"
                                        cy="50%"
                                        innerRadius={60}
                                        outerRadius={80}
                                        paddingAngle={5}
                                        dataKey="value"
                                    />

                                    <Tooltip
                                        formatter={(value) => [toPersianNumber(Number(value)), 'تعداد']}
                                        contentStyle={{ direction: 'rtl', fontFamily: 'Vazirmatn' }}
                                    />
                                </PieChart>
                            </ResponsiveContainer>
                        ) : (
                            <div className="flex items-center justify-center h-full text-gray-400">
                                {loading
                                    ? "در حال بارگذاری..."
                                    : "تسکی برای نمایش وجود ندارد"}
                            </div>
                        )}
                    </div>
                    <div className="flex justify-center gap-4 mt-2 text-sm">
                        <span className="flex items-center gap-1">
                            <div className="w-3 h-3 rounded-full bg-blue-500"></div>{" "}
                            در انتظار
                        </span>
                        <span className="flex items-center gap-1">
                            <div className="w-3 h-3 rounded-full bg-yellow-500"></div>{" "}
                            در حال انجام
                        </span>
                        <span className="flex items-center gap-1">
                            <div className="w-3 h-3 rounded-full bg-green-500"></div>{" "}
                            انجام شده
                        </span>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-lg font-bold text-gray-800">
                            تسک های نزدیک به ددلاین
                        </h2>
                        <Link
                            to="/tasks"
                            className="text-sm text-blue-600 hover:underline"
                        >
                            مشاهده همه
                        </Link>
                    </div>
                    {loading ? (
                        <p className="text-gray-400 text-center mt-10">
                            در حال بارگذاری...
                        </p>
                    ) : upcomingTasks.length === 0 ? (
                        <p className="text-gray-400 text-center mt-10">
                            تسک نزدیک به ددلاین ندارید. 🎉
                        </p>
                    ) : (
                        <div className="space-y-3">
                            {upcomingTasks.map((task) => (
                                <div
                                    key={task.id}
                                    className="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100"
                                >
                                    <div>
                                        <p className="font-semibold text-gray-800 text-sm">
                                            {toPersianNumber(task.title)}
                                        </p>
                                        <p className="text-xs text-gray-500">
                                            {toPersianNumber(task.project?.name || "بدون پروژه")}
                                        </p>
                                    </div>
                                    <span className="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">
                                        {toPersianNumber(formatToJalali(task.deadline))}
                                    </span>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
