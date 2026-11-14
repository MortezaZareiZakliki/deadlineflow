import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { AuthProvider } from "./context/AuthContext";
import ProtectedRoute from "./components/ProtectedRoute";
import PublicRoute from "./components/PublicRoute";
import Layout from "./components/Layout";
import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import Tasks from "./pages/Tasks";
import Projects from "./pages/Projects";
import Profile from "./pages/Profile";
import Notifications from "./pages/Notifications";

export default function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    <Route element={<PublicRoute />}>
                        <Route path="/login" element={<Login />} />
                    </Route>

                    <Route element={<ProtectedRoute />}>
                        <Route element={<Layout />}>
                            <Route path="/dashboard" element={<Dashboard />} />
                            <Route path="/tasks" element={<Tasks />} />
                            <Route path="/projects" element={<Projects />} />
                            <Route path="/profile" element={<Profile />} />
                            <Route
                                path="/notifications"
                                element={<Notifications />}
                            />
                        </Route>
                    </Route>

                    <Route
                        path="*"
                        element={<Navigate to="/dashboard" replace />}
                    />
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}
