import { useState, useEffect, SyntheticEvent } from "react";
import { motion, AnimatePresence } from "framer-motion";
import api from "../api/axios";
import { ProjectModalProps } from "../types";
import ErrorList from './ErrorList';

export default function ProjectModal({
    isOpen,
    onClose,
    onSave,
    editingProject,
}: ProjectModalProps) {
    const [formData, setFormData] = useState({
        name: "",
        priority: "medium",
        description: "",
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");

    useEffect(() => {
        if (isOpen) {
            if (editingProject) {
                setFormData({
                    name: editingProject.name,
                    priority: editingProject.priority,
                    description: "",
                });
            } else {
                setFormData({ name: "", priority: "medium", description: "" });
            }
        }
    }, [isOpen, editingProject]);

    const handleSubmit = async (e: SyntheticEvent) => {
        e.preventDefault();
        setLoading(true);
        setError("");

        try {
            if (editingProject) {
                await api.put(`/projects/${editingProject.id}`, formData);
            } else {
                await api.post("/projects", formData);
            }
            onSave();
            onClose();
        } catch (err: any) {
            setError(err.response?.data?.errors || err.response?.data?.message || 'خطا در ذخیره پروژه');
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
                    className="bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-md border border-white/20"
                >
                    <h2 className="text-xl font-bold mb-4">
                        {editingProject ? "ویرایش پروژه" : "ساخت پروژه جدید"}
                    </h2>

                    {error && <ErrorList errors={error} />}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm text-gray-700 mb-1">
                                نام پروژه
                            </label>
                            <input
                                type="text"
                                value={formData.name}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        name: e.target.value,
                                    })
                                }
                                className="w-full border border-gray-300 rounded px-3 py-2"
                                required
                            />
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
