<?php

namespace Kenepa\TranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Kenepa\TranslationManager\Resources\LanguageLineResource;

class EditLanguageLine extends EditRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        //dd($data);
        foreach ($data['text'] as $locale => $translation) {
            $data['translations'][] = [
                'language' => $locale,
                'text' => $translation,
            ];
        }

        foreach ($data['tenants_text'] ?? [] as $tenant => $translation) {
            foreach ($translation as $locale => $text) {
                $data['tenancy_translations'][] = [
                    'language' => $locale,
                    $tenant => $text,
                ];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['text'] = [];
        foreach ($data['translations'] as $translation) {
            $data['text'][$translation['language']] = $translation['text'];
            
            // check for tenant specific translations
            foreach ($data['tenancy_translations'] as $tenant) {
                $key = array_keys($tenant)[1];
                $data['tenants_text'][$key] = [
                    $translation['language'] => $tenant[$key]
                ];
            }
        }

        unset($data['translations']);
        unset($data['tenancy_translations']);

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->record->flushGroupCache();
    }

    protected function getActions(): array
    {
        return [];
    }
}
