package com.example.vendorvalidation;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;

import java.io.InputStream;

@Service
public class VendorValidationService {
    public ValidationResult validate(MultipartFile pdf) {
        ValidationResult result = new ValidationResult();
        try (InputStream is = pdf.getInputStream(); PDDocument document = PDDocument.load(is)) {
            String text = new PDFTextStripper().getText(document).toLowerCase();
            // Normalize whitespace
            text = text.replaceAll("\\s+", " ").trim();
            System.out.println("Extracted PDF text: " + text); // Debug: print extracted text

            // Flexible keyword checks
            boolean hasRevenue = text.contains("annual revenue") && text.contains("$");
            boolean hasNoBankruptcy = text.contains("bankruptcy: none");
            boolean hasReference1 = text.contains("reference letter 1");
            boolean hasReference2 = text.contains("reference letter 2");
            boolean hasReferences = hasReference1 && hasReference2;
            boolean hasCompliance = text.contains("compliance certificate") || text.contains("iso");
            boolean hasRegistration = text.contains("business registration certificate");
            boolean hasYears = text.contains("years in business: 4") || text.contains("years in business: 3") || text.contains("years in business: 5");

            boolean hasNetAssets = false;
            int netAssetsIdx = text.indexOf("net assets:");
            if (netAssetsIdx != -1) {
                int endIdx = text.indexOf(" ", netAssetsIdx + 12);
                if (endIdx == -1) endIdx = text.length();
                String netAssetsStr = text.substring(netAssetsIdx + 12, endIdx).replaceAll("[^0-9$]", "").replace("$", "");
                try {
                    double netAssets = Double.parseDouble(netAssetsStr);
                    hasNetAssets = netAssets >= 30000;
                } catch (Exception ignore) {}
            }
            boolean noLegalDisputes = text.contains("no major legal disputes in the last 3 years");
            boolean noRegulatoryViolations = text.contains("no regulatory violations in the last 3 years");

            // Financial stability
            result.setFinancialStability((hasRevenue && hasNoBankruptcy && hasNetAssets) ? 1.0 : 0.0);
            // Reputation
            result.setReputation((hasReferences && hasYears && noLegalDisputes) ? 1.0 : 0.0);
            // Compliance
            result.setCompliance((hasCompliance && hasRegistration && noRegulatoryViolations) ? 1.0 : 0.0);

            // Extract vendor name (simple extraction for demo)
            String vendorName = "Unknown Vendor";
            int idx = text.indexOf("vendor name:");
            if (idx != -1) {
                int endIdx = text.indexOf(".", idx);
                if (endIdx != -1) {
                    vendorName = text.substring(idx + 12, endIdx).trim();
                }
            }
            result.setVendorName(vendorName);
            result.setPdfFileName(pdf.getOriginalFilename());

            if (result.getFinancialStability() == 1.0 && result.getReputation() == 1.0 && result.getCompliance() == 1.0) {
                result.setStatus("approved");
                result.setNotes("All criteria met. Scheduling facility visit.");
                result.setVisitScheduled(true);
            } else {
                result.setStatus("rejected");
                StringBuilder notes = new StringBuilder("Missing or insufficient: ");
                if (hasRevenue == false) notes.append("annual revenue; ");
                if (hasNoBankruptcy == false) notes.append("bankruptcy; ");
                if (hasNetAssets == false) notes.append("net assets; ");
                if (hasReferences == false) notes.append("reference letters; ");
                if (hasYears == false) notes.append("years in business; ");
                if (noLegalDisputes == false) notes.append("no major legal disputes; ");
                if (hasCompliance == false) notes.append("compliance certificate; ");
                if (hasRegistration == false) notes.append("business registration certificate; ");
                if (noRegulatoryViolations == false) notes.append("no regulatory violations; ");
                result.setNotes(notes.toString());
                result.setVisitScheduled(false);
            }
        } catch (Exception e) {
            result.setStatus("rejected");
            result.setNotes("Error processing PDF: " + e.getMessage());
        }
        return result;
    }
} 