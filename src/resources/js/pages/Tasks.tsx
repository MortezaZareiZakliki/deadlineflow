import { useEffect, useState, useRef } from "react";
import api from "../api/axios";
import { Task } from "../types";
import TaskModal from "../components/TaskModal";
import ConfirmModal from "../components/ConfirmModal";
import { formatToJalali } from "../utils/date";
import { toPersianNumber } from "../utils/number";

const priorityColors = {
    high: "bg-red-100 text-red-700",
    medium: "bg-yellow-100 text-yellow-700",
    low: "bg-green-100 text-green-700",
};

const statusLabels = {
    todo: "در انتظار",
    in_progress: "در حال انجام",
    done: "انجام شده",
};

const proirityLabels = {
    high: "بالا",
    medium: "متوسط",
    low: "کم",
};

export default function Tasks() {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [loading, setLoading] = useState(true);
    const hasFetched = useRef(false);

    const [isTaskModalOpen, setIsTaskModalOpen] = useState(false);
    const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);
    const [selectedTask, setSelectedTask] = useState<Task | null>(null);
    const [deleting, setDeleting] = useState(false);

    const fetchTasks = () => {
        setLoading(true);
        api.get("/tasks")
            .then((response) => setTasks(response.data.data))
            .catch((err) => console.error("Error loading tasks:", err))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;

        fetchTasks();
    }, []);

    const handleEdit = (task: Task) => {
        setSelectedTask(task);
        setIsTaskModalOpen(true);
    };

    const handleDeleteClick = (task: Task) => {
        setSelectedTask(task);
        setIsConfirmModalOpen(true);
    };

    const handleConfirmDelete = async () => {
        if (!selectedTask) return;
        setDeleting(true);
        try {
            await api.delete(`/tasks/${selectedTask.id}`);
            setIsConfirmModalOpen(false);
            fetchTasks();
        } catch (err) {
            console.error(err);
        } finally {
            setDeleting(false);
        }
    };

    const groupedTasks = tasks.reduce(
        (acc, task) => {
            const projectName = task.project?.name || "بدون پروژه";
            if (!acc[projectName]) {
                acc[projectName] = [];
            }
            acc[projectName].push(task);
            return acc;
        },
        {} as Record<string, Task[]>,
    );

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">تسک ها</h1>
                <button
                    onClick={() => {
                        setSelectedTask(null);
                        setIsTaskModalOpen(true);
                    }}
                    className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    + تسک جدید
                </button>
            </div>
            {loading ? (
                <div className="text-center py-8">در حال بارگذاری...</div>
            ) : tasks.length === 0 ? (
                <div className="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                    هیچ تسکی وجود ندارد.
                </div>
            ) : (
                <div className="space-y-8">
                    {Object.entries(groupedTasks).map(
                        ([projectName, projectTasks]) => (
                            <div
                                key={projectName}
                                className="bg-gray-50/50 rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm"
                            >
                                <div className="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                                    <h2 className="text-lg font-bold text-gray-800 flex items-center gap-2">
                                        <span className="bg-blue-100 text-blue-700 p-2 rounded-lg text-sm">
                                            📁
                                        </span>
                                        {toPersianNumber(projectName)}
                                    </h2>
                                    <span className="bg-white text-gray-600 text-sm font-semibold px-3 py-1 rounded-full border border-gray-200 shadow-sm">
                                        {toPersianNumber(projectTasks.length)} تسک
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    {projectTasks.map((task) => (
                                        <div
                                            key={task.id}
                                            className="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 hover:shadow-md transition-shadow"
                                        >
                                            <div>
                                                <p className="font-semibold text-gray-800">
                                                    {toPersianNumber(task.title)}
                                                </p>
                                                <p className="text-sm text-gray-500 mt-1">
                                                    ددلاین:{" "}
                                                    {toPersianNumber(formatToJalali(
                                                        task.deadline,
                                                    ))}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                                {task.version && (
                                                    <span className="bg-purple-50 text-purple-700 px-2 py-1 rounded font-medium border border-purple-100">
                                                        نسخه{" "}
                                                        {
                                                            toPersianNumber(task.version
                                                                .version_number)
                                                        }
                                                    </span>
                                                )}
                                            </div>
                                            <div className="flex items-center gap-4">
                                                <div className="flex items-center gap-2">
                                                    <span
                                                        className={`px-3 py-1 rounded-full text-xs font-medium ${priorityColors[task.priority]}`}
                                                    >
                                                        {proirityLabels[task.priority]}
                                                    </span>
                                                    <span className="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                                        {
                                                            statusLabels[
                                                            task.status
                                                            ]
                                                        }
                                                    </span>
                                                </div>
                                                <div className="flex gap-2 border-r pr-4 border-gray-200">
                                                    <button
                                                        onClick={() =>
                                                            handleEdit(task)
                                                        }
                                                        className="text-blue-600 text-sm hover:underline"
                                                    >
                                                        ویرایش
                                                    </button>
                                                    <button
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                task,
                                                            )
                                                        }
                                                        className="text-red-600 text-sm hover:underline"
                                                    >
                                                        حذف
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ),
                    )}
                </div>
            )}

            <TaskModal
                isOpen={isTaskModalOpen}
                onClose={() => setIsTaskModalOpen(false)}
                onSave={fetchTasks}
                editingTask={selectedTask}
            />

            <ConfirmModal
                isOpen={isConfirmModalOpen}
                onClose={() => setIsConfirmModalOpen(false)}
                onConfirm={handleConfirmDelete}
                title="حذف تسک"
                message={`آیا از حذف تسک «${selectedTask?.title}» مطمئن هستید؟`}
                loading={deleting}
            />
        </div>
    );
}
