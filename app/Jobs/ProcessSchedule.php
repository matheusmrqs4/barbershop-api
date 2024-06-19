<?php

namespace App\Jobs;

use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Models\API\Entity\Service;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSchedule implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $barber;
    protected $daysToAdd;

    /**
     * Create a new job instance.
     */
    public function __construct(Barber $barber, $daysToAdd)
    {
        $this->barber = $barber;
        $this->daysToAdd = $daysToAdd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            $services = Service::all();
            $scheduleTimes = [];

            $start_time = Carbon::createFromTimeString($this->barber->start_time);
            $end_time = Carbon::createFromTimeString($this->barber->end_time);

            $current_date = Carbon::now();
            $end_date = $current_date->copy()->addDays($this->daysToAdd);

        while ($current_date->lte($end_date)) {
            $current_time = $start_time->copy();
            while ($current_time->lte($end_time)) {
                if ($current_time->dayOfWeek !== 0) {
                    foreach ($services as $service) {
                        $serviceID = $service->id;
                        $scheduleTime = $current_date->format('Y-m-d') . ' ' . $current_time->format('H:i:s');
                        $formattedSchedule = Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTime);

                        $newSchedule = new Schedule([
                            'schedule' => $formattedSchedule,
                            'services_id' => $serviceID,
                            'barbers_id' => $this->barber->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $newSchedule->save();

                        $scheduleTimes[] = $formattedSchedule;
                    }
                }
                $current_time->addMinutes((int) $service->duration);
            }
            $current_date->addDay();
        }
    }
}
