import { motion, AnimatePresence } from "framer-motion";
import { ConfirmModalProps } from "../types/index"

export default function ConfirmModal({ isOpen, onClose, onConfirm, title, message, loading }: ConfirmModalProps) {
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
          <h3 className="text-lg font-bold text-gray-800 mb-2">{title}</h3>
          <p className="text-gray-600 mb-6">{message}</p>
          <div className="flex justify-center gap-3">
            <button onClick={onClose} className="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">انصراف</button>
            <button onClick={onConfirm} disabled={loading} className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50">
              {loading ? 'در حال حذف...' : 'بله، حذف کن'}
            </button>
          </div>
        </motion.div>
      </motion.div>
    </AnimatePresence>
  );
}