<x-layout>
    <x-slot:title>Welcome to GODRIC</x-slot:title>

    <p>Welcome to the GODRIC campaigns management system. You can record your participation in campaigns here - and if you hold a representative or communications role, also get access to additional reports.</p>

    <p>We ask you to record your participation in certain campaign actions, as this is essential to get an overall view of where our strength is, and ultimately to win! Effective practical solidarity requires not just participating, but participating visibly so that your colleagues can feel stronger as a result. You will also benefit by not receiving some targeted communications relating to campaign actions you've already taken.</p>

    <h2>Campaign Actions</h2>
    
    @if ($campaigns->count() == 0)
	<p>There are no active campaign actions at the moment.</p>
    @else

	@foreach ($campaigns as $campaign)




	    

	
	@endforeach	

    @endif
    
</x-layout>
