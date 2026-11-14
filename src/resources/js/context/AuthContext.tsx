import {
    createContext,
    useContext,
    useState,
    useEffect,
    ReactNode,
} from "react";
import api from "../api/axios";
import { User, AuthContextType } from "../types";

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const storedUser = localStorage.getItem("user");
        if (storedUser) {
            setUser(JSON.parse(storedUser));
        }
        setIsLoading(false);
    }, []);

    useEffect(() => {
        if (user) {
            localStorage.setItem("user", JSON.stringify(user));
        }
    }, [user]);

    const login = async (email: string, password: string) => {
        const response = await api.post("/login", { email, password });
        const { token, user } = response.data.data;
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));
        setUser(user);
    };

    const logout = async () => {
        try {
            const token = localStorage.getItem("token");
            if (token) {
                await api.post("/logout");
            }
        } catch (error) {
            console.error("Logout error", error);
        } finally {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            setUser(null);
        }
    };

    const updateUser = (updatedUser: User) => {
        setUser(updatedUser);
        localStorage.setItem("user", JSON.stringify(updatedUser));
    };

    return (
        <AuthContext.Provider
            value={{ user, login, logout, updateUser, isLoading }}
        >
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) throw new Error("useAuth must be used within AuthProvider");
    return context;
}
