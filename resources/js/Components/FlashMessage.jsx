import { useState } from 'react'
import { usePage } from '@inertiajs/react'
import { useEffect } from 'react';

const FlashMessage = () => {
    const { flash } = usePage().props;
    const [flashIMsg, setFlashIMsg] = useState(null);
    const [flashSMsg, setFlashSMsg] = useState(null);
    const [flashEMsg, setFlashEMsg] = useState(null);

    useEffect(() => {
        setFlashIMsg(flash?.message?.info);
        setFlashSMsg(flash?.message?.success);
        setFlashEMsg(flash?.message?.error);

        // Play sound when there is a new flash message
        // if (flash?.message) {
        //     const audio = new Audio('/path/to/your/soundfile.mp3');
        //     // Configurable Options
        //     audio.volume = 0.5;  // Set volume (0.0 to 1.0)
        //     audio.loop = false;  // Set to true if you want the sound to loop
        //     audio.playbackRate = 1.0;  // Speed of playback, 1.0 is normal speed
        //     audio.muted = false;  // Mute the sound if needed
        //     audio.play().catch(error => {
        //         console.log('Error playing sound:', error);
        //     });
        // }
    }, [flash]);

    setTimeout(() => {
        setFlashIMsg(null);
        setFlashSMsg(null);
        setFlashEMsg(null);
    }, 5000);


    return (
        <span>
        {flashIMsg && (
            <div className="absolute top-24 right-6 bg-blue-500 p-2 text-white text-sm rounded-md shadow-lg z-50">
                {flashIMsg} 
            </div>
        )}
        {flashSMsg && (
            <div className="absolute top-24 right-6 bg-green-500 p-2 text-white text-sm rounded-md shadow-lg z-50">
                {flashSMsg} 
            </div>
        )}
        {flashEMsg && (
            <div className="absolute top-24 right-6 bg-rose-500 p-2 text-white text-sm rounded-md shadow-lg z-50">
                {flashEMsg} 
            </div>
        )}
        </span>
    )

}
export default FlashMessage;
