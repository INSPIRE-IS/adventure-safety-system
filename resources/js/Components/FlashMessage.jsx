import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import { useToast } from "@/context/ToastProvider";

const FlashMessage = () => {
    const { flash } = usePage().props;
    const { addToast } = useToast();

    useEffect(() => {
        if (flash?.message?.info) addToast(flash.message.info, "info");
        if (flash?.message?.success) addToast(flash.message.success, "success");
        if (flash?.message?.error) addToast(flash.message.error, "error");
    }, [flash]);

    return null; // No need to render anything
};

export default FlashMessage;
