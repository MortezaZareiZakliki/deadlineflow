import { useState, useEffect, SyntheticEvent } from "react";
import api from "../api/axios";
import { motion, AnimatePresence } from "framer-motion";
import ConfirmModal from "./ConfirmModal";
import { toPersianNumber } from "../utils/number";
import { Version, VersionModalProps } from "../types/index";
import ErrorList from './ErrorList';

export default function VersionModal({
    isOpen,
    onClose,
    projectId,
    projectName,
    onVersionChange,
}: VersionModalProps) {
    const [versions, setVersions] = useState<Version[]>([]);
    const [versionNumber, setVersionNumber] = useState("");
    const [description, setDescription] = useState("");
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(false);

    const [isConfirmOpen, setIsConfirmOpen] = useState(false);
    const [deletingVersion, setDeletingVersion] = useState<Version | null>(
        null,
    );
    const [isDeleting, setIsDeleting] = useState(false);

    const fetchVersions = () => {
        if (!projectId) return;
        api.get(`/projects/${projectId}/versions`)
            .then((res) => setVersions(res.data.data))
            .catch((err) => console.error(err));
    };

    useEffect(() => {
        if (isOpen && projectId) {
            fetchVersions();
            setVersionNumber("");
            setDescription("");
            setError("");
        }
    }, [isOpen, projectId]);

    const handleSubmit = async (e: SyntheticEvent) => {
        e.preventDefault();
        setLoading(true);
        setError("");
        try {
            await api.post(`/projects/${projectId}/versions`, {
                version_number: versionNumber,
                description,
            });
            fetchVersions();
            if (onVersionChange) onVersionChange();
            setVersionNumber("");
            setDescription("");
        } catch (err: any) {
            setError(err.response?.data?.errors || err.response?.data?.message || 'خطا در ساخت نسخه');
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteClick = (version: Version) => {
        setDeletingVersion(version);
        setIsConfirmOpen(true);
    };

    const handleConfirmDelete = async () => {
        if (!deletingVersion) return;
        setIsDeleting(true);
        try {
            await api.delete(`/versions/${deletingVersion.id}`);
            setIsConfirmOpen(false);
            fetchVersions();
            if (onVersionChange) onVersionChange();
        } catch (err: any) {
            setIsConfirmOpen(false);
            alert(err.response?.data?.message || "خطا در حذف نسخه");
        } finally {
            setIsDeleting(false);
        }
    };

    if (!isOpen) return null;

    return (
        <>
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
                        className="bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-white/20"
                    >
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-xl font-bold text-gray-800">
                                نسخه های {projectName}
                            </h2>
                            <button
                                onClick={onClose}
                                className="text-gray-500 hover:text-red-500 text-xl font-bold"
                            >
                                ×
                            </button>
                        </div>

                        {error && <ErrorList errors={error} />}

                        <form
                            onSubmit={handleSubmit}
                            className="flex gap-2 mb-6 items-end"
                        >
                            <div className="flex-1">
                                <label className="block text-xs text-gray-600 mb-1">
                                    شماره نسخه
                                </label>
                                <input
                                    type="text"
                                    placeholder="1.0.0"
                                    value={versionNumber}
                                    onChange={(e) =>
                                        setVersionNumber(e.target.value)
                                    }
                                    className="w-full border p-2 rounded"
                                    required
                                />
                            </div>
                            <div className="flex-1">
                                <label className="block text-xs text-gray-600 mb-1">
                                    توضیحات (اختیاری)
                                </label>
                                <input
                                    type="text"
                                    value={description}
                                    onChange={(e) =>
                                        setDescription(e.target.value)
                                    }
                                    className="w-full border p-2 rounded"
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={loading}
                                className="bg-blue-600 text-white px-4 py-2 rounded h-[42px] disabled:opacity-50"
                            >
                                {loading ? "..." : "افزودن"}
                            </button>
                        </form>

                        <div className="space-y-2">
                            {versions.length === 0 ? (
                                <p className="text-gray-500 text-sm text-center py-4">
                                    هیچ نسخه ای برای این پروژه ثبت نشده است.
                                </p>
                            ) : (
                                versions.map((v) => (
                                    <div
                                        key={v.id}
                                        className="flex justify-between items-center p-3 bg-gray-50 rounded border border-gray-100"
                                    >
                                        <div>
                                            <span className="font-bold text-blue-700">
                                                {toPersianNumber(v.version_number)}
                                            </span>
                                            {v.description && (
                                                <p className="text-xs text-gray-500">
                                                    {v.description}
                                                </p>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <span className="text-xs bg-white border px-2 py-1 rounded text-gray-600">
                                                {toPersianNumber(v.tasks_count || 0)} تسک
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleDeleteClick(v)
                                                }
                                                className="text-red-500 text-sm hover:underline"
                                            >
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </motion.div>
                </motion.div>
            </AnimatePresence>

            <ConfirmModal
                isOpen={isConfirmOpen}
                onClose={() => setIsConfirmOpen(false)}
                onConfirm={handleConfirmDelete}
                title="حذف نسخه"
                message={`آیا از حذف نسخه «${toPersianNumber(deletingVersion?.version_number)}» مطمئن هستید؟`}
                loading={isDeleting}
            />
        </>
    );
}
