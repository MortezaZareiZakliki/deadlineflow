import { useEffect, useState, useRef } from "react";
import api from "../api/axios";
import { Project } from "../types";
import ProjectModal from "../components/ProjectModal";
import VersionModal from "../components/VersionModal";
import ConfirmModal from "../components/ConfirmModal";
import { toPersianNumber } from "../utils/number";

const priorityColors = {
    high: "bg-red-100 text-red-700",
    medium: "bg-yellow-100 text-yellow-700",
    low: "bg-green-100 text-green-700",
};

const proirityLabels = {
    high: "بالا",
    medium: "متوسط",
    low: "کم",
};

export default function Projects() {
    const [projects, setProjects] = useState<Project[]>([]);
    const [loading, setLoading] = useState(true);
    const hasFetched = useRef(false);

    const [isProjectModalOpen, setIsProjectModalOpen] = useState(false);
    const [selectedProject, setSelectedProject] = useState<Project | null>(
        null,
    );

    const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);

    const [isVersionModalOpen, setIsVersionModalOpen] = useState(false);
    const [activeVersionProject, setActiveVersionProject] = useState<{
        id: number;
        name: string;
    } | null>(null);

    const fetchProjects = () => {
        setLoading(true);
        api.get("/projects")
            .then((response) => setProjects(response.data.data))
            .catch((err) => console.error("Error loading projects:", err))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchProjects();
    }, []);

    const handleEdit = (project: Project) => {
        setSelectedProject(project);
        setIsProjectModalOpen(true);
    };

    const handleDeleteClick = (project: Project) => {
        setSelectedProject(project);
        setIsConfirmModalOpen(true);
    };

    const handleConfirmDelete = async () => {
        if (!selectedProject) return;
        setDeleting(true);
        try {
            await api.delete(`/projects/${selectedProject.id}`);
            setIsConfirmModalOpen(false);
            fetchProjects();
        } catch (err) {
            console.error(err);
        } finally {
            setDeleting(false);
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">
                    مدیریت پروژه ها
                </h1>
                <button
                    onClick={() => {
                        setSelectedProject(null);
                        setIsProjectModalOpen(true);
                    }}
                    className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    + پروژه جدید
                </button>
            </div>

            {loading ? (
                <div className="text-center py-8">در حال بارگذاری...</div>
            ) : projects.length === 0 ? (
                <div className="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                    هیچ پروژه ای وجود ندارد.
                </div>
            ) : (
                <div className="space-y-3">
                    {projects.map((project) => (
                        <div
                            key={project.id}
                            className="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 hover:shadow-md transition-shadow"
                        >
                            <div>
                                <p className="font-semibold text-gray-800">
                                    {toPersianNumber(project.name)}
                                </p>
                                <div className="flex items-center gap-2 mt-2">
                                    <span
                                        className={`px-3 py-1 rounded-full text-xs font-medium ${priorityColors[project.priority]}`}
                                    >
                                        {proirityLabels[project.priority]}
                                    </span>
                                    <span className="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                        {toPersianNumber(project.tasks_count || 0)} تسک
                                    </span>
                                </div>
                            </div>
                            <div className="flex items-center gap-3 border-r pr-4 border-gray-200">
                                <button
                                    onClick={() => {
                                        setActiveVersionProject({
                                            id: project.id,
                                            name: project.name,
                                        });
                                        setIsVersionModalOpen(true);
                                    }}
                                    className="text-green-600 hover:text-green-800 text-sm font-medium flex items-center gap-1"
                                >
                                    مدیریت نسخه ها
                                    <span className="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                                        {toPersianNumber(project.versions_count || 0)}
                                    </span>
                                </button>
                                <button
                                    onClick={() => handleEdit(project)}
                                    className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    ویرایش
                                </button>
                                <button
                                    onClick={() => handleDeleteClick(project)}
                                    className="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    حذف
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <ProjectModal
                isOpen={isProjectModalOpen}
                onClose={() => setIsProjectModalOpen(false)}
                onSave={fetchProjects}
                editingProject={selectedProject}
            />

            <ConfirmModal
                isOpen={isConfirmModalOpen}
                onClose={() => setIsConfirmModalOpen(false)}
                onConfirm={handleConfirmDelete}
                title="حذف پروژه"
                message={`آیا از حذف پروژه «${selectedProject?.name}» مطمئن هستید؟`}
                loading={deleting}
            />

            <VersionModal
                isOpen={isVersionModalOpen}
                onClose={() => setIsVersionModalOpen(false)}
                projectId={activeVersionProject?.id || null}
                projectName={activeVersionProject?.name || ""}
                onVersionChange={fetchProjects}
            />
        </div>
    );
}
