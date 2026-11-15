import { useState, useEffect, SyntheticEvent } from "react";
import { motion, AnimatePresence } from "framer-motion";
import api from "../api/axios";
import { Project, TaskModalProps } from "../types";
import { formatToJalaliShort } from "../utils/date";
import ReactMultiDatePicker from "react-multi-date-picker";
const DatePicker =
    (ReactMultiDatePicker as any).default || ReactMultiDatePicker;
// const DateObject =
//     (ReactMultiDatePicker as any).DateObject ||
//     (ReactMultiDatePicker as any).default?.DateObject;
import persian from "react-date-object/calendars/persian";
import persian_fa from "react-date-object/locales/persian_fa";
import gregorian from "react-date-object/calendars/gregorian";
import ErrorList from './ErrorList';

export default function TaskModal({
    isOpen,
    onClose,
    onSave,
    editingTask,
}: TaskModalProps) {
    const [projects, setProjects] = useState<Project[]>([]);
    const [formData, setFormData] = useState({
        project_id: "",
        version_id: "",
        title: "",
        description: "",
        status: "todo",
        priority: "medium",
        deadline: "",
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [versions, setVersions] = useState<any[]>([]);
    const [jalaliDeadline, setJalaliDeadline] = useState("");

    useEffect(() => {
        if (isOpen) {
            api.get("/projects")
                .then((res) => {
                    const fetchedProjects = res.data.data;
                    setProjects(fetchedProjects);
                })
                .catch((err) => {
                    console.error("Error fetching projects:", err);
                });

            if (editingTask) {
                setFormData({
                    project_id: editingTask.project?.id?.toString() || "",
                    version_id: editingTask.version?.id?.toString() || "",
                    title: editingTask.title,
                    description: editingTask.description || "",
                    status: editingTask.status,
                    priority: editingTask.priority,
                    deadline: editingTask.deadline || "",
                });

                setJalaliDeadline(
                    editingTask.deadline
                        ? formatToJalaliShort(editingTask.deadline)
                        : "",
                );
            } else {
                setFormData({
                    project_id: "",
                    version_id: "",
                    title: "",
                    description: "",
                    status: "todo",
                    priority: "medium",
                    deadline: "",
                });

                setJalaliDeadline("");
            }
        }
    }, [isOpen, editingTask]);

    useEffect(() => {
        if (formData.project_id) {
            api.get(`/projects/${formData.project_id}/versions`)
                .then((res) => setVersions(res.data.data))
                .catch((err) => console.error(err));
        } else {
            setVersions([]);
        }
    }, [formData.project_id]);

    const handleSubmit = async (e: SyntheticEvent) => {
        e.preventDefault();
        setLoading(true);
        setError("");

        try {
            if (editingTask) {
                await api.put(`/tasks/${editingTask.id}`, formData);
            } else {
                await api.post("/tasks", formData);
            }
            onSave();
            onClose();
        } catch (err: any) {
            setError(err.response?.data?.errors || err.response?.data?.message || 'خطا در ذخیره تسک');
        } finally {
            setLoading(false);
        }
    };

    if (!isOpen) return null;

    return (
        <AnimatePresence>
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            >
                <motion.div
                    initial={{ opacity: 0, y: 20, scale: 0.95 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, y: 20, scale: 0.95 }}
                    transition={{ duration: 0.2 }}
                    className="bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-white/20"
                >
                    <h2 className="text-xl font-bold mb-4">
                        {editingTask ? "ویرایش تسک" : "ساخت تسک جدید"}
                    </h2>

                    {error && <ErrorList errors={error} />}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                پروژه
                            </label>
                            <select
                                value={formData.project_id}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        project_id: e.target.value,
                                    })
                                }
                                className="w-full border border-gray-300 rounded px-3 py-2"
                                required
                            >
                                <option value="" disabled>
                                    انتخاب کنید
                                </option>
                                {projects.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                نسخه (الزامی)
                            </label>
                            <select
                                value={formData.version_id}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        version_id: e.target.value,
                                    })
                                }
                                className="w-full border border-gray-300 rounded px-3 py-2"
                                required
                                disabled={
                                    !formData.project_id ||
                                    versions.length === 0
                                }
                            >
                                <option value="" disabled>
                                    {versions.length === 0
                                        ? "ابتدا برای این پروژه نسخه بسازید"
                                        : "انتخاب کنید"}
                                </option>
                                {versions.map((v) => (
                                    <option key={v.id} value={v.id}>
                                        {v.version_number}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                عنوان تسک
                            </label>
                            <input
                                type="text"
                                value={formData.title}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        title: e.target.value,
                                    })
                                }
                                className="w-full border border-gray-300 rounded px-3 py-2"
                                required
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm text-gray-700 mb-1">
                                    وضعیت
                                </label>
                                <select
                                    value={formData.status}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            status: e.target.value,
                                        })
                                    }
                                    className="w-full border border-gray-300 rounded px-3 py-2"
                                >
                                    <option value="todo">در انتظار</option>
                                    <option value="in_progress">
                                        در حال انجام
                                    </option>
                                    <option value="done">انجام شده</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm text-gray-700 mb-1">
                                    اولویت
                                </label>
                                <select
                                    value={formData.priority}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            priority: e.target.value,
                                        })
                                    }
                                    className="w-full border border-gray-300 rounded px-3 py-2"
                                >
                                    <option value="low">پایین</option>
                                    <option value="medium">متوسط</option>
                                    <option value="high">بالا</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                ددلاین
                            </label>
                            <DatePicker
                                calendar={persian}
                                locale={persian_fa}
                                calendarPosition="bottom-right"
                                value={jalaliDeadline}
                                format="YYYY/MM/DD"
                                onChange={(date: any) => {
                                    if (date) {
                                        setJalaliDeadline(
                                            date.format("YYYY/MM/DD"),
                                        );

                                        const gregorianDate = date.convert(gregorian, persian_fa);
                                        const formatted = gregorianDate
                                            .format("YYYY-MM-DD")
                                            .replace(/[۰-۹]/g, (d: string) => String("۰۱۲۳۴۵۶۷۸۹".indexOf(d)));

                                        setFormData({ ...formData, deadline: formatted });
                                    } else {
                                        setJalaliDeadline("");
                                        setFormData({
                                            ...formData,
                                            deadline: "",
                                        });
                                    }
                                }}
                                inputClass="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                                containerClassName="w-full"
                            />
                        </div>

                        <div className="flex justify-end gap-2 mt-6">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded"
                            >
                                انصراف
                            </button>
                            <button
                                type="submit"
                                disabled={loading}
                                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                            >
                                {loading ? "در حال ذخیره..." : "ذخیره"}
                            </button>
                        </div>
                    </form>
                </motion.div>
            </motion.div>
        </AnimatePresence>
    );
}
