export interface User {
  id: number;
  name: string;
  email: string;
  avatar: string | null;
}

export interface Project {
  id: number;
  name: string;
  description: string | null;
  priority: 'low' | 'medium' | 'high';
  deadline: string | null;
  tasks_count?: number;
  versions_count?: number;
}

export interface Task {
  id: number;
  title: string;
  description: string | null;
  status: 'todo' | 'in_progress' | 'done';
  priority: 'low' | 'medium' | 'high';
  deadline: string | null;
  project: {
    id: number;
    name: string;
  } | null;
  version: {
    id: number,
    version_number: string;
  } | null,
  created_at: string;
}

export interface ConfirmModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
  title: string;
  message: string;
  loading?: boolean;
}

export interface ProjectModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: () => void;
  editingProject: Project | null;
}

export interface TaskModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: () => void;
  editingTask: Task | null;
}

export interface Version {
  id: number;
  version_number: string;
  description: string;
  release_date: string;
  tasks_count?: number;
}

export interface VersionModalProps {
  isOpen: boolean;
  onClose: () => void;
  projectId: number | null;
  projectName: string;
  onVersionChange?: () => void;
}

export interface AuthContextType {
  user: User | null;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  updateUser: (updatedUser: User) => void;
  isLoading: boolean;
}

export interface Stats {
  todo: number;
  in_progress: number;
  done: number;
  total: number;
}

export interface UpcomingTask {
  id: number;
  title: string;
  deadline: string;
  project: { name: string } | null;
}

export interface ProjectSummary {
  id: number;
  name: string;
  tasks_count: number;
}

export interface NotificationItem {
  id: string;
  type: string;
  data: any;
  read_at: string | null;
  created_at: string;
}

export interface Session {
  id: number;
  ip_address: string;
  user_agent: string;
  browser: string;
  platform: string;
  device_type: string;
  last_used_at: string;
  created_at: string;
  is_current: boolean;
}

export interface ErrorListProps {
  errors: Record<string, string[]> | string | null | undefined;
}