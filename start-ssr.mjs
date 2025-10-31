import { exec } from 'child_process';

exec('php artisan inertia:start-ssr', (error, stdout, stderr) => {
    if (error) {
        console.error(`Error: ${error.message}`);
        process.exit(1);
    }
    if (stderr) {
        console.error(`Stderr: ${stderr}`);
        process.exit(1);
    }
    console.log(`Stdout: ${stdout}`);
});
