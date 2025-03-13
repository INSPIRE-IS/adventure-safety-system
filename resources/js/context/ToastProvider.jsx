import { createContext, useContext, useState } from "react";

const ToastContext = createContext();

export const useToast = () => useContext(ToastContext);

export const ToastProvider = ({ children }) => {
    const [toasts, setToasts] = useState([]);

    const addToast = (type = "info", message) => {
        const id = Date.now();
        setToasts([...toasts, { id, message, type }]);

        // Auto-remove toast after 5s
        setTimeout(() => {
            setToasts((prev) => prev.filter((toast) => toast.id !== id));
        }, 5000);
    };

    return (
        <ToastContext.Provider value={{ addToast }}>
            {children}
            <div className="fixed top-5 right-5 z-50">
                {toasts.map((toast) => (
                    <div
                        key={toast.id}
                        className={`mb-2 px-4 py-2 text-white rounded-md shadow-md ${
                            toast.type === "success"
                                ? "bg-green-500"
                                : toast.type === "error"
                                ? "bg-red-500"
                                : "bg-blue-500"
                        }`}
                    >
                        {toast.message}
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};
